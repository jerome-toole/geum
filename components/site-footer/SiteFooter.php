<?php

namespace Geum\Components;

use Geum\Component;
use Geum\ComponentBase;

/**
 * SiteFooter Component
 *
 * Usage:
 *   use Geum\Components\SiteFooter;
 *
 *   echo SiteFooter::make();
 */
class SiteFooter extends ComponentBase
{
    protected static string $name = 'site-footer';

    protected static function getDefaults(): array
    {
        return [
            'background_color' => 'brand-2',
        ];
    }

    /**
     * Create a new SiteFooter component.
     *
     * @return static|null Returns null if component should not render.
     */
    public static function make(
        array $classes = [],
        ?string $background_color = null,
        ...$others
    ): ?static {
        return static::createFromArgs(static::mergeArgs(get_defined_vars()));
    }

    /**
     * Transform args before rendering.
     */
    protected static function transform(array $args): array
    {
        if (have_rows('footer_images', 'option')) {
            $args['content']['images'] = [];

            while (have_rows('footer_images', 'options')) {
                the_row();

                if (! empty(get_sub_field('image'))) {
                    $image = get_sub_field('image');
                    $image['size'] = 'medium';
                }

                if (! empty($image)) {
                    $link = get_sub_field('link');
                    $image_data = [
                        'image' => $image ?? null,
                        'link' => $link,
                    ];

                    // Pre-create link args for images with links
                    if (! empty($link)) {
                        $image_data['link_args'] = array_merge($link, [
                            'classes' => ['site-footer__image', 'img-fit'],
                            'content' => \Geum\Components\Image::make(...$image),
                            'content_filter' => false,
                        ]);
                    }

                    $args['content']['images'][] = $image_data;
                }
            }
        }

        return $args;
    }
}
