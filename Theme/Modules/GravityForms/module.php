<?php

namespace Theme\Modules\GravityForms;

class Module
{
    public static function init(): void
    {
        // Prevent <head> GF hooks script on pages it's not needed
        // https://docs.gravityforms.com/gform_force_hooks_js_output/
        \add_filter('gform_force_hooks_js_output', '__return_false');

        // Move header GF scripts to footer (includes jQuery)
        // https://docs.gravityforms.com/gform_enqueue_scripts/
        \add_filter('gform_enqueue_scripts', [__CLASS__, 'moveScriptsToFooter']);

        // Disable default theme CSS
        // https://docs.gravityforms.com/gform_disable_form_theme_css/
        \add_filter('gform_disable_form_theme_css', '__return_true');

        // Override default GF initial settings
        \add_filter('gform_form_settings_initial_values', [__CLASS__, 'setInitialSettings']);

        // Sanitize confirmation message (uses wp_kses_post)
        // https://docs.gravityforms.com/gform_sanitize_confirmation_message/
        \add_filter('gform_sanitize_confirmation_message', '__return_true');

        // Auto-scroll to confirmation/validation on submission
        // https://docs.gravityforms.com/gform_confirmation_anchor/
        \add_filter('gform_confirmation_anchor', '__return_true');

        // Disable automatic updates
        // https://docs.gravityforms.com/gform_disable_auto_update/
        \add_filter('gform_disable_auto_update', '__return_true');
        \add_filter('option_gform_enable_background_updates', '__return_false');
    }

    public static function moveScriptsToFooter(): void
    {
        \wp_script_add_data('gform_gravityforms', 'group', 1);
        \wp_script_add_data('gform_json', 'group', 1);
    }

    public static function setInitialSettings(array $initial_values): array
    {
        $initial_values['labelPlacement'] = 'top_label';
        $initial_values['descriptionPlacement'] = 'above';
        $initial_values['subLabelPlacement'] = 'above';
        $initial_values['validationSummary'] = true;
        $initial_values['enableHoneypot'] = true;
        $initial_values['enableAnimation'] = false;

        return $initial_values;
    }
}
