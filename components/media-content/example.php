<?php

/**
 * MediaContent Component Examples
 */

use Geum\Components\MediaContent;

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
    <h2 class="component-example-section__title">Media Content - Image Left</h2>
    <p class="component-example-section__description">Image on left, content on right.</p>
    <div class="component-example-section__preview">
        <?php if ($sample_id) { ?>
            <?= MediaContent::make(
                heading: 'About Our Services',
                subheading: 'Quality you can trust',
                content: '<p>We provide exceptional services tailored to your needs. Our team of experts is dedicated to delivering outstanding results.</p>',
                image: ['id' => $sample_id],
                media_type: 'image',
                media_side: 'left',
            ); ?>
        <?php } else { ?>
            <p><em>No images in media library for this example.</em></p>
        <?php } ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Media Content - Image Right</h2>
    <p class="component-example-section__description">Content on left, image on right.</p>
    <div class="component-example-section__preview">
        <?php if ($sample_id) { ?>
            <?= MediaContent::make(
                heading: 'Our Process',
                content: '<p>From initial consultation to final delivery, we ensure a smooth and efficient process.</p>',
                image: ['id' => $sample_id],
                media_type: 'image',
                media_side: 'right',
            ); ?>
        <?php } else { ?>
            <p><em>No images in media library for this example.</em></p>
        <?php } ?>
    </div>
</section>
