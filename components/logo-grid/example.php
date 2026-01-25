<?php

/**
 * LogoGrid Component Examples
 *
 * Note: Requires image attachment IDs for logos.
 */

use Geum\Components\LogoGrid;

// Get sample images
$attachments = get_posts([
    'post_type' => 'attachment',
    'post_mime_type' => 'image',
    'posts_per_page' => 6,
    'post_status' => 'inherit',
]);

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Logo Grid</h2>
    <p class="component-example-section__description">Grid of logo images.</p>
    <div class="component-example-section__preview">
        <?php if (count($attachments) >= 3) { ?>
            <?= LogoGrid::make(
                columns: '4',
                logos: [
                    ['image' => ['id' => $attachments[0]->ID]],
                    ['image' => ['id' => $attachments[1]->ID]],
                    ['image' => ['id' => $attachments[2]->ID]],
                    ['image' => ['id' => $attachments[0]->ID]],
                ],
            ); ?>
        <?php } else { ?>
            <p><em>Need at least 3 images in media library for this example.</em></p>
        <?php } ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Logo Grid with Links</h2>
    <p class="component-example-section__description">Logos that link to external sites.</p>
    <div class="component-example-section__preview">
        <?php if (count($attachments) >= 2) { ?>
            <?= LogoGrid::make(
                columns: '3',
                logos: [
                    [
                        'image' => ['id' => $attachments[0]->ID],
                        'link' => ['url' => 'https://example.com', 'title' => 'Example'],
                    ],
                    [
                        'image' => ['id' => $attachments[1]->ID],
                        'link' => ['url' => 'https://example.org', 'title' => 'Example Org'],
                    ],
                ],
            ); ?>
        <?php } else { ?>
            <p><em>Need at least 2 images in media library for this example.</em></p>
        <?php } ?>
    </div>
</section>
