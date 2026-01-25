<?php

namespace Geum\WordPress;

class Escaping
{
    public static function init(): void
    {
        \add_action('wp_kses_allowed_html', [__CLASS__, 'filterWPKsesAllowedHtml'], 10, 2);
    }

    public static function filterWPKsesAllowedHtml($tags, $context): array
    {
        if ($context === 'post') {
            $tags['iframe'] = [
                'src' => true,
                'height' => true,
                'width' => true,
                'frameborder' => true,
                'allowfullscreen' => true,
                'loading' => true,
            ];

            $tags['img']['sizes'] = true;
            $tags['img']['srcset'] = true;

            $tags['a']['tabindex'] = true;
        }

        return $tags;
    }
}
