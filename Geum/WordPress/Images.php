<?php

namespace Geum\WordPress;

use Geum\Config;

class Images
{
    public static function init(): void
    {
        \add_action('after_setup_theme', [__CLASS__, 'registerImageSizes']);
        \add_filter('image_size_names_choose', [__CLASS__, 'setThemeImageSizeNames']);
        \add_filter('intermediate_image_sizes_advanced', [__CLASS__, 'removeUnwantedImageSizes']);
        \add_filter('jpeg_quality', [__CLASS__, 'setDefaultJPEGQuality']);
        \add_filter('big_image_size_threshold', [__CLASS__, 'setMaxImageSize']);
    }

    public static function registerImageSizes(): void
    {
        \add_theme_support('post-thumbnails');
        \set_post_thumbnail_size(150, 150, true);

        $geum_sizes = Config::get('image_sizes', []);
        if (! empty($geum_sizes)) {
            foreach ($geum_sizes as $size_id => $size_info) {
                if ($size_info === false) {
                    continue;
                }

                if (empty($size_info[2])) {
                    $size_info[2] = false;
                }

                \add_image_size(
                    $size_id,
                    $size_info[0],
                    $size_info[1],
                    $size_info[2]
                );
            }
        }
    }

    /**
     * Set the nice names for each image.
     *
     * @link https://developer.wordpress.org/reference/hooks/image_size_names_choose/
     */
    public static function setThemeImageSizeNames(array $sizes): array
    {
        $geum_sizes = Config::get('image_sizes', []);
        if (! empty($geum_sizes)) {
            foreach ($geum_sizes as $size_id => $size) {
                if ($size === false) {
                    continue;
                }

                $sizes[$size_id] = ucwords(str_replace('_', ' ', str_replace('geum_', '', $size_id)));
            }
        }

        return $sizes;
    }

    /**
     * Prevent images for unwanted default sizes being generated on upload.
     *
     * @link https://developer.wordpress.org/reference/hooks/intermediate_image_sizes_advanced/
     */
    public static function removeUnwantedImageSizes(array $sizes): array
    {
        $geum_sizes = Config::get('image_sizes', []);
        if (! empty($geum_sizes)) {
            foreach ($sizes as $size_id => $size) {
                if (array_key_exists($size_id, $geum_sizes) && $geum_sizes[$size_id] === false) {
                    unset($sizes[$size_id]);
                }
            }
        }

        return $sizes;
    }

    /**
     * Set the default JPEG compression quality on upload.
     *
     * @link https://developer.wordpress.org/reference/hooks/jpeg_quality/
     */
    public static function setDefaultJPEGQuality(int $quality): int
    {
        return Config::get('jpeg_upload_quality', $quality);
    }

    /**
     * Set the threshold (in pixels) at which images will be downscaled.
     *
     * @link https://developer.wordpress.org/reference/hooks/big_image_size_threshold/
     */
    public static function setMaxImageSize(int $threshold): int
    {
        return Config::get('max_image_size', $threshold);
    }
}
