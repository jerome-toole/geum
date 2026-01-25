<?php

/**
 * Accordion Component Examples
 *
 * Available variables:
 * - $faker: Faker\Generator instance for generating mock data
 * - $component: The current component name
 */

use Geum\Component;

?>

<!-- Example: Basic Accordion -->
<section class="component-example-section">
    <h2 class="component-example-section__title">Basic Accordion</h2>
    <p class="component-example-section__description">
        A simple accordion with three items.
    </p>
    <div class="component-example-section__preview">
        <?= \Geum\Components\Accordion::make(
            accordion_items: [
                [
                    'title' => 'What is your return policy?',
                    'content' => '<p>'.$faker->paragraph(3).'</p>',
                ],
                [
                    'title' => 'How long does shipping take?',
                    'content' => '<p>'.$faker->paragraph(2).'</p>',
                ],
                [
                    'title' => 'Do you offer international shipping?',
                    'content' => '<p>'.$faker->paragraph(4).'</p>',
                ],
            ],
        ); ?>
    </div>
</section>

<!-- Example: Accordion with Heading -->
<section class="component-example-section">
    <h2 class="component-example-section__title">Accordion with Heading</h2>
    <p class="component-example-section__description">
        An accordion with a section heading above the items.
    </p>
    <div class="component-example-section__preview">
        <?= \Geum\Components\Accordion::make(
            heading: 'Frequently Asked Questions',
            accordion_items: [
                [
                    'title' => $faker->sentence(4),
                    'content' => '<p>'.$faker->paragraph(3).'</p>',
                ],
                [
                    'title' => $faker->sentence(5),
                    'content' => '<p>'.$faker->paragraph(2).'</p><p>'.$faker->paragraph(2).'</p>',
                ],
                [
                    'title' => $faker->sentence(3),
                    'content' => '<p>'.$faker->paragraph(4).'</p>',
                ],
            ],
        ); ?>
    </div>
</section>

<!-- Example: Accordion with Rich Content -->
<section class="component-example-section">
    <h2 class="component-example-section__title">Accordion with Rich Content</h2>
    <p class="component-example-section__description">
        An accordion demonstrating rich HTML content within panels, including lists and multiple paragraphs.
    </p>
    <div class="component-example-section__preview">
        <?= \Geum\Components\Accordion::make(
            heading: 'Product Information',
            accordion_items: [
                [
                    'title' => 'Features & Specifications',
                    'content' => '
                        <p>'.$faker->paragraph(2).'</p>
                        <ul>
                            <li>'.$faker->sentence(6).'</li>
                            <li>'.$faker->sentence(5).'</li>
                            <li>'.$faker->sentence(7).'</li>
                            <li>'.$faker->sentence(4).'</li>
                        </ul>
                    ',
                ],
                [
                    'title' => 'Care Instructions',
                    'content' => '
                        <p><strong>Washing:</strong> '.$faker->sentence(8).'</p>
                        <p><strong>Drying:</strong> '.$faker->sentence(6).'</p>
                        <p><strong>Storage:</strong> '.$faker->sentence(7).'</p>
                    ',
                ],
                [
                    'title' => 'Warranty Information',
                    'content' => '
                        <p>'.$faker->paragraph(3).'</p>
                        <p>'.$faker->paragraph(2).'</p>
                    ',
                ],
            ],
        ); ?>
    </div>
</section>

<!-- Example: Single Item Accordion -->
<section class="component-example-section">
    <h2 class="component-example-section__title">Single Item Accordion</h2>
    <p class="component-example-section__description">
        An accordion with just one expandable item.
    </p>
    <div class="component-example-section__preview">
        <?= \Geum\Components\Accordion::make(
            accordion_items: [
                [
                    'title' => 'Click to reveal more information',
                    'content' => '<p>'.$faker->paragraph(5).'</p>',
                ],
            ],
        ); ?>
    </div>
</section>

<!-- Example: Many Items -->
<section class="component-example-section">
    <h2 class="component-example-section__title">Many Items</h2>
    <p class="component-example-section__description">
        An accordion with many items to test scrolling and performance.
    </p>
    <div class="component-example-section__preview">
        <?php
        $items = [];
for ($i = 1; $i <= 8; $i++) {
    $items[] = [
        'title' => $faker->sentence(rand(3, 6)),
        'content' => '<p>'.$faker->paragraph(rand(2, 4)).'</p>',
    ];
}
?>
        <?= \Geum\Components\Accordion::make(
            heading: 'Full FAQ Section',
            accordion_items: $items,
        ); ?>
    </div>
</section>
