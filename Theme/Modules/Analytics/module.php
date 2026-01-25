<?php

namespace Theme\Modules\Analytics;

class Module
{
    public static function init(): void
    {
        \add_action('wp_head', [__CLASS__, 'outputGtmHead']);
        \add_action('wp_body_open', [__CLASS__, 'outputGtmBody']);
    }

    public static function outputGtmHead(): void
    {
        $gtm_code = \get_field('gtm_code', 'option');

        if (empty($gtm_code)) {
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

        if (empty($gtm_code)) {
            return;
        }

        ?>
        <!-- Google Tag Manager (noscript) -->
        <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=<?= esc_js($gtm_code); ?>"
            height="0" width="0" style="display:none;visibility:hidden"></iframe></noscript>
        <!-- End Google Tag Manager (noscript) -->
        <?php
    }
}
