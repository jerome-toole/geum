<?php

/**
 * Image Component Examples
 *
 * Note: Requires valid WordPress attachment IDs. Examples below use placeholder IDs.
 */

use Geum\Components\Image;

// Try to get a real attachment ID from the media library
$attachments = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 1,
    'post_status' => 'inherit',
]);
$sample_id = ! empty($attachments) ? $attachments[0]->ID : null;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Basic Image</h2>
    <p class="component-example-section__description">Image from WordPress media library.</p>
    <div class="component-example-section__preview">
        <?php if ($sample_id) { ?>
            <?= Image::make(id: $sample_id); ?>
        <?php } else { ?>
            <p><em>No images in media library. Upload an image to see this example.</em></p>
        <?php } ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Image Sizes</h2>
    <p class="component-example-section__description">Different registered image sizes.</p>
    <div class="component-example-section__preview">
        <?php if ($sample_id) { ?>
            <div style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: flex-start;">
                <?= Image::make(id: $sample_id, size: 'thumbnail'); ?>
                <?= Image::make(id: $sample_id, size: 'medium'); ?>
            </div>
        <?php } else { ?>
            <p><em>No images in media library.</em></p>
        <?php } ?>
    </div>
</section>
