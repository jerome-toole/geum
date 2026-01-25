<?php

namespace Geum;

class Helpers
{
    public static function startsWith($haystack, $needle): bool
    {
        $length = strlen($needle);

        return substr($haystack, 0, $length) === $needle;
    }

    /**
     * Builds an HTML string from an array of attribute key-value pairs.
     *
     * Valid attribute value types are: scalars (int, float, string, and bool) and arrays.
     * An empty string is considered a valid value; equivalent to a `true` boolean.
     *
     * @link https://developer.mozilla.org/en-US/docs/Web/HTML/Attributes#boolean_attributes
     *
     * @param  array  $attributes  An array of attribute key-value pairs.
     * @return string A HTML string containing space-separated, escaped HTML element attributes.
     */
    public static function buildAttributes(array $attributes = []): string
    {
        if (empty($attributes)) {
            return '';
        }

        $html = array_map(
            function ($key, $val) {
                if (! isset($val) || (! is_scalar($val) && ! is_array($val))) {
                    return ''; // invalid value type.
                } elseif (is_bool($val)) {
                    return $val ? esc_html($key) : '';
                } elseif (is_array($val)) {
                    // Filter out nested arrays - only keep scalar values
                    $val = array_filter($val, 'is_scalar');

                    if (empty($val)) {
                        return '';
                    }

                    if ($key === 'style') {
                        // Build CSS declarations for 'style' attribute.
                        $val = array_reduce(
                            array_keys($val),
                            function ($carry, $k) use ($val) {
                                if (! is_numeric($k) && is_scalar($val[$k])) {
                                    $carry[] = "$k: $val[$k];";
                                }

                                return $carry;
                            },
                            []
                        );
                    } else {
                        $val = array_filter($val, function ($i) {
                            return is_scalar($i) && (! empty($i) || is_numeric($i));
                        });
                    }

                    $val = array_unique($val);
                    $val = implode(' ', $val);
                }

                $key = \esc_html($key);
                $val = self::isUrlAttribute($key) ? \esc_url(trim($val)) : \esc_attr(trim($val));

                return "$key=\"$val\"";
            },
            array_keys($attributes),
            $attributes
        );

        return implode(' ', $html);
    }

    /**
     * Determines whether the given attribute must contain a "valid" URL string.
     *
     * Excludes the itemid, itemprop, and ping attributes, as they may not always contain a URL, or
     * may contain a space-separated list of URLs.
     *
     * @link https://url.spec.whatwg.org/#valid-url-string
     * @link https://html.spec.whatwg.org/multipage/indices.html#attributes-3
     *
     * @param  string  $attribute  The attribute name.
     * @return bool Whether the attribute must contain a URL.
     */
    public static function isUrlAttribute(string $attribute): bool
    {
        return ! empty($attribute) && in_array($attribute, [
            // Attributes that must contain URL strings.
            'action',
            'cite', // https://developer.mozilla.org/en-US/docs/Web/HTML/Element/blockquote#attr-cite
            'data', // https://developer.mozilla.org/en-US/docs/Web/HTML/Element/object#attr-data
            'formaction',
            'href',
            'poster',
            'src',
        ], true);
    }

    /**
     * Builds an HTML classes string from variadic arguments.
     *
     * Accepts strings, arrays, or nested arrays. Flattens, dedupes, and escapes.
     *
     * @param  mixed  ...$args  Strings or arrays of class names.
     * @return string An escaped string of classes.
     */
    public static function classes(...$args): string
    {
        $classes = self::flattenClasses($args);

        if (empty($classes)) {
            return '';
        }

        return \esc_attr(implode(' ', array_unique($classes)));
    }

    /**
     * Recursively flattens class arguments into a single array.
     */
    private static function flattenClasses(array $items): array
    {
        $result = [];

        foreach ($items as $item) {
            if (is_string($item) && trim($item) !== '') {
                $result[] = trim($item);
            } elseif (is_array($item)) {
                $result = array_merge($result, self::flattenClasses($item));
            }
        }

        return $result;
    }

    /**
     * @deprecated Use classes() instead.
     */
    public static function buildClasses(array $classes = []): string
    {
        return self::classes($classes);
    }

    /**
     * Prints a formatted error to the log. Optionally echoes it.
     *
     * @param  $message  An error (array, object, string)
     * @param  $echo  Whether to echo the error (if WP_DEBUG_DISPLAY is true)
     */
    public static function errorLog($message, $echo = false)
    {
        if (WP_DEBUG === true) {
            if (is_array($message) || is_object($message)) {
                error_log(var_export($message, true));
            } else {
                error_log($message);
            }

            if (WP_DEBUG_DISPLAY === true && $echo === true) {
                self::debug($message);
            }
        }
    }

    /**
     * Echoes a message in a formatted <pre> container.
     *
     * @param  $message  A message to print
     * @param  $die  Whether to die() after printing the message
     */
    public static function debug($message, $die = false)
    {
        ini_set('highlight.default', '#222;');
        ini_set('highlight.html', '#808080');
        ini_set('highlight.keyword', '#912d72; font-weight: bold;');
        ini_set('highlight.string', '#112468;');
        ini_set('highlight.comment', '#222');

        if (WP_DEBUG === true && WP_DEBUG_DISPLAY === true) {
            echo "
                <pre style='
                    font-size: 14px;
                    padding: 1em;
                    color: #222;
                    background: #eee;
                    border-radius: 9px;
                    overflow-wrap: break-word;
                '>
            ";
            highlight_string("<?php\n".var_export($message, true).";\n?>");
            echo '</pre>';

            if ($die) {
                exit();
            }
        }
    }

    /**
     * Determines whether a given object is an instance of a given set of classes.
     *
     * @param  object  $object  The object to check.
     * @param  array  $classNames  An array of class names to validate against. Default empty array.
     */
    public static function isValidClass($object, $classNames = []): bool
    {
        foreach ($classNames as $className) {
            if (is_a($object, $className)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines whether the query is for any existing taxonomy archive page.
     *
     * Combines WordPress's `is_tax()`, `is_category()`, and `is_tag()` functions.
     *
     * @param  string|string[]  $taxonomy  Taxonomy slug or slugs to check against.
     * @param  int|string|int[]|string[]  $term  Term ID, name, slug, or array of such to check against.
     * @return bool Whether the query is for an existing taxonomy archive page (custom or built-in).
     */
    public static function isTaxonomy($taxonomy = '', $term = ''): bool
    {
        if ($taxonomy === 'category') {
            return \is_category($term);
        } elseif ($taxonomy === 'post_tag') {
            return \is_tag($term);
        } elseif (! empty($taxonomy)) {
            return \is_tax($taxonomy, $term);
        }

        return \is_tax($taxonomy, $term) || \is_category($term) || \is_tag($term);
    }

    /**
     * Returns URL for static assets (images, fonts) that works in both dev and production.
     *
     * In dev mode, returns Vite dev server URL.
     * In production, returns public folder URL.
     *
     * @param  string  $path  Asset path relative to assets/static/ (e.g., 'images/icons/search.svg')
     * @return string The full asset URL
     */
    public static function staticUrl(string $path): string
    {
        $path = ltrim($path, '/');

        if (\Geum\Vite::isRunning()) {
            return \Geum\Vite::getDevServerUrl().'/'.$path;
        }

        return \Geum\Paths::assetURL('build/'.$path);
    }
}
