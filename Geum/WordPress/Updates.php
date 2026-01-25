<?php

namespace Geum\WordPress;

class Updates
{
    public static function init(): void
    {
        /**
         * Enable auto updates for themes and plugins
         *
         * @link https://wordpress.org/support/article/configuring-automatic-background-updates/#plugin-theme-updates-via-filter
         */
        \add_filter('auto_update_theme', '__return_true');
        \add_filter('auto_update_plugin', '__return_true');

        /**
         * Disable auto-update user interface(s).
         *
         * @link https://make.wordpress.org/core/2020/07/15/controlling-plugin-and-theme-auto-updates-ui-in-wordpress-5-5/
         */
        \add_filter('themes_auto_update_enabled', '__return_false');
        \add_filter('plugins_auto_update_enabled', '__return_false');

        /**
         * Explicitly disallow major & dev core updates, but allow minor/security updates.
         */
        \add_filter('allow_major_auto_core_updates', '__return_false');
        \add_filter('allow_minor_auto_core_updates', '__return_true');
        \add_filter('allow_dev_auto_core_updates', '__return_false');

        /**
         * Remove Site Health recommendation that there may be problems with plugin and theme auto-updates.
         */
        \add_filter('site_status_tests', [__CLASS__, 'removeSiteHealthAutoUpdateRecommendation']);

        /**
         * Disable email notifications for auto-updates.
         */
        \add_filter('auto_plugin_update_email', '__return_false');
        \add_filter('auto_theme_update_email', '__return_false');
    }

    /**
     * Filter which site status tests are run on a site to remove the plugin and theme auto-updates test.
     *
     * @link https://developer.wordpress.org/reference/hooks/site_status_tests/
     *
     * @param  array  $tests  An associative array of direct and asynchronous tests.
     * @return array The filtered tests array.
     */
    public static function removeSiteHealthAutoUpdateRecommendation(array $tests): array
    {
        unset($tests['direct']['plugin_theme_auto_updates']);

        return $tests;
    }
}
