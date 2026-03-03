<?php

namespace Theme\Modules\Analytics;

class AnalyticsModule
{
    public static function init(): void
    {
        \add_action('wp_head', [__CLASS__, 'outputConsentDefaults'], 1);
        \add_action('wp_head', [__CLASS__, 'outputGtmHead']);
        \add_action('wp_head', [__CLASS__, 'outputHeadScripts'], 20);
        \add_action('wp_body_open', [__CLASS__, 'outputGtmBody']);
        \add_action('wp_body_open', [__CLASS__, 'outputBodyScripts'], 20);

        // Prevent ACF from stripping <script> tags from the script HTML fields on save
        \add_filter('acf/update_value/key=field_head_script_html', fn ($value) => $value, 10, 1);
        \add_filter('acf/update_value/key=field_body_script_html', fn ($value) => $value, 10, 1);
    }

    public static function outputConsentDefaults(): void
    {
        if (! \get_field('gtm_enabled', 'option') || empty(\get_field('gtm_code', 'option'))) {
            return;
        }

        $consent = isset($_COOKIE['cookies']) && $_COOKIE['cookies'] === 'accept'
            ? 'granted'
            : 'denied';

        $privacy_first = (bool) \get_field('analytics_privacy_first', 'option');

        ?>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('consent', 'default', {
                analytics_storage:  '<?= $consent ?>',
                ad_storage:         '<?= $consent ?>',
                ad_user_data:       '<?= $consent ?>',
                ad_personalization: '<?= $consent ?>',
            });
            <?php if ($privacy_first) { ?>
            gtag('set', 'allow_google_signals', false);
            gtag('set', 'allow_ad_personalization_signals', false);
            <?php } ?>
        </script>
        <?php
    }

    public static function outputGtmHead(): void
    {
        $gtm_code = \get_field('gtm_code', 'option');

        if (! \get_field('gtm_enabled', 'option') || empty($gtm_code)) {
            return;
        }

        ?>
        <!-- Google Tag Manager -->
        <script>(function(w,d,s,l,i){w[l]=w[l]||[];w[l].push({'gtm.start':
        new Date().getTime(),event:'gtm.js'});var f=d.getElementsByTagName(s)[0],
        j=d.createElement(s),dl=l!='dataLayer'?'&l='+l:'';j.async=true;j.src=
        'https://www.googletagmanager.com/gtm.js?id='+i+dl;f.parentNode.insertBefore(j,f);
        })(window,document,'script','dataLayer','<?= esc_js($gtm_code); ?>');</script>
        <!-- End Google Tag Manager -->
        <?php
    }

    public static function outputGtmBody(): void
    {
        $gtm_code = \get_field('gtm_code', 'option');

        if (! \get_field('gtm_enabled', 'option') || empty($gtm_code)) {
            return;
        }

        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= esc_js($gtm_code); ?>"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }

    public static function outputHeadScripts(): void
    {
        $rows = \get_field('head_scripts', 'option');

        if (empty($rows)) {
            return;
        }

        $consented = isset($_COOKIE['cookies']) && $_COOKIE['cookies'] === 'accept';

        foreach ($rows as $row) {
            $html = $row['head_script_html'] ?? '';

            if (empty($html)) {
                continue;
            }

            if (! empty($row['head_script_requires_consent']) && ! $consented) {
                echo '<script type="text/plain" data-cookie-consent data-consent-location="head">'."\n";
                echo $html."\n";
                echo '</script>'."\n";
            } else {
                echo $html."\n";
            }
        }
    }

    public static function outputBodyScripts(): void
    {
        $rows = \get_field('body_scripts', 'option');

        if (empty($rows)) {
            return;
        }

        $consented = isset($_COOKIE['cookies']) && $_COOKIE['cookies'] === 'accept';

        foreach ($rows as $row) {
            $html = $row['body_script_html'] ?? '';

            if (empty($html)) {
                continue;
            }

            if (! empty($row['body_script_requires_consent']) && ! $consented) {
                echo '<script type="text/plain" data-cookie-consent data-consent-location="body">'."\n";
                echo $html."\n";
                echo '</script>'."\n";
            } else {
                echo $html."\n";
            }
        }
    }
}
