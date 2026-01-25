<?php

/**
 * Cards Component Examples
 */

use Geum\Components\Cards;

// Get sample posts
$sample_posts = get_posts([
    'posts_per_page' => 3,
    'post_status' => 'publish',
]);

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Cards from Recent Posts</h2>
    <p class="component-example-section__description">Grid of cards from recent blog posts.</p>
    <div class="component-example-section__preview">
        <?= Cards::make(
            card_source: 'recent',
            post_type: 'post',
            limit: 3,
            columns: '3',
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Cards with Custom Content</h2>
    <p class="component-example-section__description">Cards with manually defined content.</p>
    <div class="component-example-section__preview">
        <?= Cards::make(
            columns: '3',
            items: [
                [
                    'content' => [
                        'heading' => 'Feature One',
                        'text' => 'Description of the first feature.',
                        'link' => ['url' => '/feature-1', 'title' => 'Learn more'],
                    ],
                ],
                [
                    'content' => [
                        'heading' => 'Feature Two',
                        'text' => 'Description of the second feature.',
                        'link' => ['url' => '/feature-2', 'title' => 'Learn more'],
                    ],
                ],
                [
                    'content' => [
                        'heading' => 'Feature Three',
                        'text' => 'Description of the third feature.',
                        'link' => ['url' => '/feature-3', 'title' => 'Learn more'],
                    ],
                ],
            ],
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Two Column Cards</h2>
    <p class="component-example-section__description">Cards in a two-column layout.</p>
    <div class="component-example-section__preview">
        <?= Cards::make(
            columns: '2',
            items: [
                [
                    'content' => [
                        'heading' => 'Left Card',
                        'text' => 'Content for the left card.',
                    ],
                ],
                [
                    'content' => [
                        'heading' => 'Right Card',
                        'text' => 'Content for the right card.',
                    ],
                ],
            ],
        ); ?>
    </div>
</section>
