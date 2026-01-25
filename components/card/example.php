<?php

/**
 * Card Component Examples
 */

use Geum\Components\Card;

// Get sample post
$sample_post = get_posts([
    'posts_per_page' => 1,
    'post_status' => 'publish',
])[0] ?? null;

// Get sample image
$attachments = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 1,
    'post_status' => 'inherit',
]);
$sample_image_id = ! empty($attachments) ? $attachments[0]->ID : null;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Card from WP_Post</h2>
    <p class="component-example-section__description">Card automatically populated from a WordPress post.</p>
    <div class="component-example-section__preview">
        <?php if ($sample_post) { ?>
            <?= Card::make(object: $sample_post); ?>
        <?php } else { ?>
            <p><em>No published posts found.</em></p>
        <?php } ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Manual Card</h2>
    <p class="component-example-section__description">Card with manually defined content.</p>
    <div class="component-example-section__preview">
        <?= Card::make(
            content: [
                'heading' => 'Custom Card Title',
                'text' => 'This is a manually created card with custom content.',
                'link' => [
                    'url' => '/learn-more',
                    'title' => 'Learn More',
                ],
            ],
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Card with Image</h2>
    <p class="component-example-section__description">Card featuring an image.</p>
    <div class="component-example-section__preview">
        <?php if ($sample_image_id) { ?>
            <?= Card::make(
                content: [
                    'heading' => 'Featured Article',
                    'text' => 'A card with a featured image.',
                    'image' => ['id' => $sample_image_id],
                    'link' => [
                        'url' => '/article',
                        'title' => 'Read Article',
                    ],
                ],
            ); ?>
        <?php } else { ?>
            <p><em>No images in media library.</em></p>
        <?php } ?>
    </div>
</section>
