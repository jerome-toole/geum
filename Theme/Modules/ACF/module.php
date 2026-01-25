<?php

namespace Theme\Modules\ACF;

class Module
{
    public static function init(): void
    {
        \add_action('acf/init', [__CLASS__, 'optionPages']);
        \add_action('acf/init', [__CLASS__, 'setACFGoogleAPIKey']);

        \add_action('acf/init', [__CLASS__, 'fixPreviews']);
        \add_action('acf/init', [__CLASS__, 'disableShortcode']);

        \add_filter('gutenberg_can_edit_post_type', [__CLASS__, 'disableGutenberg'], 10, 2);
        \add_filter('use_block_editor_for_post_type', [__CLASS__, 'disableGutenberg'], 10, 2);

        \add_filter('acf/fields/wysiwyg/toolbars', [__CLASS__, 'filterEditorToolbarTypes']);

        \add_filter('acf/load_field/name=theme_background_color', [__CLASS__, 'loadColorFieldChoices']);
        \add_filter('acf/load_field/name=item_theme_background_color', [__CLASS__, 'loadColorFieldChoices']);

        \add_filter('acf/load_value/type=link', [__CLASS__, 'filterEmptyLinkField']);

        \add_action('acf/render_field_presentation_settings/type=wysiwyg', [__CLASS__, 'addStripHtmlFieldSetting']);
        \add_filter('acf/format_value/type=wysiwyg', [__CLASS__, 'stripFieldValueHtmlTags'], 20, 3);
    }

    public static function optionPages(): void
    {
        $options_pages = [
            _x('General', 'ACF options page name', 'geum'),
            _x('Header', 'ACF options page name', 'geum'),
            _x('Footer', 'ACF options page name', 'geum'),
            _x('Banner', 'ACF options page name', 'geum'),
        ];

        if (empty($options_pages)) {
            return;
        }

        \acf_add_options_page();

        foreach ($options_pages as $page) {
            \acf_add_options_sub_page($page);
        }
    }

    public static function setACFGoogleAPIKey(): void
    {
        $option = \get_field('google_api_key', 'option');

        if (empty($option)) {
            return;
        }

        \acf_update_setting('google_api_key', $option);
    }

    public static function loadColorFieldChoices(array $field): array
    {
        $field['choices']['none'] = __('None', 'geum');

        if (defined('GEUM_COLOR_PALETTE')) {
            foreach (GEUM_COLOR_PALETTE as $color) {
                $field['choices'][$color['slug']] = $color['name'];
            }
        }

        return $field;
    }

    public static function disableGutenberg(bool $can_edit, string $post_type): bool
    {
        if (! (\is_admin() && ! empty($_GET['post']))) {
            return $can_edit;
        }

        if (self::disableEditor($_GET['post'])) {
            $can_edit = false;
        }

        return $can_edit;
    }

    public static function disableEditor($id = false): bool
    {
        $excluded_templates = [
            // 'page-templates/example-template.php',
        ];

        if (empty($id)) {
            return false;
        }

        $id = intval($id);
        $template = \get_page_template_slug($id);

        return in_array($template, $excluded_templates);
    }

    public static function filterEditorToolbarTypes($toolbars): array
    {
        unset($toolbars['Basic']);
        unset($toolbars['Full']);

        $toolbars['Basic Formatting'] = [
            1 => \apply_filters('geum/acf/fields/wysiwyg/toolbars/basic', [
                'bold',
                'italic',
                'link',
                'unlink',
                'removeformat',
                'undo',
                'redo',
            ]),
        ];

        $toolbars['Extended Formatting'] = [
            1 => \apply_filters('geum/acf/fields/wysiwyg/toolbars/extended', [
                'formatselect',
                'bold',
                'italic',
                'bullist',
                'numlist',
                'link',
                'unlink',
                'removeformat',
                'undo',
                'redo',
            ]),
        ];

        return $toolbars;
    }

    public static function fixPreviews(): void
    {
        if (\current_user_can('edit_posts') && class_exists('acf_revisions')) {
            $acf_revs_cls = \acf()->revisions;
            \remove_filter('acf/validate_post_id', [$acf_revs_cls, 'acf_validate_post_id', 10]);
        }
    }

    public static function disableShortcode(): void
    {
        \acf_update_setting('enable_shortcode', false);
    }

    public static function filterEmptyLinkField($value): array
    {
        if (! is_array($value)) {
            return [];
        }

        return $value;
    }

    public static function addStripHtmlFieldSetting($field): void
    {
        \acf_render_field_setting($field, [
            'label' => 'Strip unwanted HTML tags?',
            'instructions' => htmlentities('Removes most tags from the loaded field value, including <p> tags.
                Useful for content that will be displayed in an <h1-6> tag.'),
            'name' => 'strip_html_tags',
            'type' => 'true_false',
            'ui' => 1,
        ]);
    }

    public static function stripFieldValueHtmlTags(mixed $value, int|string $post_id, array $field): mixed
    {
        if (empty($field['strip_html_tags'])) {
            return $value;
        }

        return strip_tags($value, [
            '<a>',
            '<br>',
            '<em>',
            '<span>',
            '<strong>',
        ]);
    }
}
