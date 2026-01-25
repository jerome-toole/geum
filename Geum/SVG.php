<?php

namespace Geum;

class SVG
{
    /**
     * Return the contents of an SVG file. Expects the SVG to be in the assets
     * directory by default.
     *
     * A full path can be passed if the SVG file is outside the assets directory.
     * For example, to retrieve an uploaded SVG from the media library, use the
     * `get_attached_file($attachment_id)`.function.
     *
     * @link https://developer.wordpress.org/reference/functions/get_attached_file/
     *
     * @param  string  $name  The filename of the SVG (incl. extension).
     * @param  array  $args  {
     *                       Optional. Arguments to create SVG return value.
     *
     * @type string $name        The filename of the SVG (incl. extension).
     * @type bool $wrapped     Whether the SVG should be wrapped in a span tag.
     * @type bool $asset       Whether the SVG path is .
     * @type string $title       A descriptive title for the SVG.
     * @type string $description A longer description of the SVG.
     * @type int $width       A specific width of the SVG.
     * @type int $height      A specific height of the SVG.
     *           }
     */
    public static function get(string $name, array $args = []): string
    {
        $svg = '';

        // Merge attributes
        $args = array_merge([
            'name' => $name,
            'wrapped' => false,
            'asset' => true,
            'title' => '',
            'description' => '',
            'width' => 0,
            'height' => 0,
        ], $args);

        // Get the path to the SVG (from assets folder by default).
        $svgPath = $args['asset'] ? self::path($args['name']) : $args['name'];

        // Not a valid SVG file path.
        if (substr($svgPath, -4, 4) !== '.svg') {
            return $svg;
        }

        // No file found.
        if (! file_exists($svgPath)) {
            return $svg;
        }

        $uniqueID = uniqid();

        // How to edit an SVG in PHP:
        // https://stackoverflow.com/questions/41264017/php-svg-editing
        // https://stackoverflow.com/questions/18758101/domdocument-add-attribute-to-root-tag

        // Create a new instance of DOMDocument.
        $doc = new \DOMDocument;

        // Load in the SVG.
        $doc->loadXML(file_get_contents($svgPath));

        // Set a role attribute on the SVG element.
        $doc->documentElement->setAttribute('role', 'img');

        // Add aria-labelledby attributes if title/description set. Otherwise, add aria-hidden.
        if ($args['title'] !== '') {
            $labelled = [];
            $labelled[] = 'title-'.$uniqueID;

            // Create a title element.
            $title = $doc->createElement('title', $args['title']);
            $title->setAttribute('id', 'title-'.$uniqueID);

            // Append it to the SVG.
            $doc->firstChild->appendChild($title);

            // Add a description to aria-labelledby if possible.
            if ($args['description'] !== '') {
                $labelled[] = 'description-'.$uniqueID;

                // Create a description element.
                $description = $doc->createElement('description', $args['description']);
                $description->setAttribute('id', 'description-'.$uniqueID);

                // Append it to the SVG.
                $doc->firstChild->appendChild($description);
            }

            // Label the SVG with element(s).
            $doc->documentElement->setAttribute('aria-labelledby', implode(' ', $labelled));
        } else {
            $doc->documentElement->setAttribute('aria-hidden', 'true');
        }

        // Try to determine the SVG's width or height if either wasn't specified in the $args array.
        if (empty($args['width']) || empty($args['height'])) {
            $svgInfo = self::info($doc);
        }

        // If width was specified, set it. Otherwise, try to set it from svgInfo.
        if (! empty($args['width'])) {
            $doc->documentElement->setAttribute('width', $args['width']);
        } elseif (! empty($svgInfo['w'])) {
            $doc->documentElement->setAttribute('width', $svgInfo['w']);
        }

        // If height was specified, set it. Otherwise, try to set it from svgInfo.
        if (! empty($args['height'])) {
            $doc->documentElement->setAttribute('height', $args['height']);
        } elseif (! empty($svgInfo['h'])) {
            $doc->documentElement->setAttribute('height', $svgInfo['h']);
        }

        // Output the SVG markup and strip the XML doctype declaration.
        // https://stackoverflow.com/questions/5706086/php-domdocument-output-without-xml-version-1-0-encoding-utf-8/17362447
        $svg = $doc->saveXML($doc->documentElement);

        if ($args['wrapped'] === true) {
            $svg = '<span class="svg-asset svg-asset--'.esc_attr($args['name']).'">'.$svg.'</span>';
        }

        return $svg;
    }

    /**
     * Build the path to the SVG asset in the theme
     */
    public static function path(string $name): string
    {
        return \Geum\Asset::path('svgs/'.$name);
    }

    /**
     * Get selected top-level attribute values from an SVG file
     *
     * @param  string|DOMDocument  $svg  so we can avoid additional DOMDocument calls
     * @param  array  $attrs  HTML attributes we want the values of
     */
    public static function attrs($svg, array $attrNames): array
    {
        $attrs = [];

        // Set up (if it's a path to an SVG file) or use DOMDocument
        if (gettype($svg) === 'string' && file_exists($svg)) {
            $doc = new \DOMDocument;
            $doc->loadXML(file_get_contents($svg));
        } else {
            $doc = $svg;
        }

        // As long as we've got a DOMDocument to work with...
        if (gettype($doc) === 'object' && get_class($doc) === 'DOMDocument') {
            // Map requested attributes to what DOMDocument returns
            foreach ($attrNames as $attrName) {
                $attrs[$attrName] = $doc->documentElement->getAttribute($attrName);
            }
        }

        return $attrs;
    }

    /**
     * Get info about an SVG file (currently just width and height)
     *
     * @param  string|DOMDocument  $svg  so we can avoid additional DOMDocument calls
     */
    public static function info($svg): array
    {
        // Defaults
        $info = [
            'w' => 'auto',
            'h' => 'auto',
        ];

        // See if we can grab the attributes
        $attrs = self::attrs($svg, ['width', 'height', 'viewBox']);

        // The width or height are missing, let's see if we can get the values from the viewBox attr
        if (empty($attrs['width']) || empty($attrs['height'])) {
            if (! empty($attrs['viewBox'])) {
                $viewboxAttrParts = explode(' ', $attrs['viewBox']);
            }
        }

        // If we can get the width directly, use that. Otherewise try and use the viewBox value
        if (! empty($attrs['width'])) {
            $info['w'] = $attrs['width'];
        } elseif (! empty($viewboxAttrParts[2])) {
            $info['w'] = $viewboxAttrParts[2];
        }

        // If we can get the height directly, use that. Otherewise try and use the viewBox value
        if (! empty($attrs['height'])) {
            $info['h'] = $attrs['height'];
        } elseif (! empty($viewboxAttrParts[3])) {
            $info['h'] = $viewboxAttrParts[3];
        }

        return $info;
    }
}
