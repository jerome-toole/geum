<?php

/**
 * Banner Component Examples
 *
 * Note: Banner uses array-style args. Requires image attachment ID.
 */

use Geum\Components\Banner;

// Get a sample image
$attachments = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 1,
    'post_status' => 'inherit',
]);
$sample_id = ! empty($attachments) ? $attachments[0]->ID : null;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Basic Banner</h2>
    <p class="component-example-section__description">Banner with image from media library.</p>
    <div class="component-example-section__preview">
        <?php if ($sample_id) { ?>
            <?= Banner::make([
                'image' => ['id' => $sample_id],
            ]); ?>
        <?php } else { ?>
            <p><em>No images in media library. Upload an image to see this example.</em></p>
        <?php } ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Banner with Custom Height</h2>
    <p class="component-example-section__description">Banner with specified image height.</p>
    <div class="component-example-section__preview">
        <?php if ($sample_id) { ?>
            <?= Banner::make([
                'image' => ['id' => $sample_id],
                'image_height' => 200,
            ]); ?>
        <?php } else { ?>
            <p><em>No images in media library.</em></p>
        <?php } ?>
    </div>
</section>
