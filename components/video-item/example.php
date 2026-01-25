<?php

/**
 * VideoItem Component Examples
 */

use Geum\Components\VideoItem;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Video with YouTube Embed</h2>
    <p class="component-example-section__description">Video item with heading and YouTube iframe.</p>
    <div class="component-example-section__preview">
        <?= VideoItem::make(
            heading: 'Watch Our Introduction',
            content: '<p>Learn more about our services in this short video.</p>',
            video: '<iframe width="560" height="315" src="https://www.youtube.com/embed/dQw4w9WgXcQ" frameborder="0" allowfullscreen></iframe>',
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Video with Vimeo Embed</h2>
    <p class="component-example-section__description">Video item with Vimeo iframe.</p>
    <div class="component-example-section__preview">
        <?= VideoItem::make(
            heading: 'Featured Video',
            video: '<iframe src="https://player.vimeo.com/video/76979871" width="640" height="360" frameborder="0" allowfullscreen></iframe>',
        ); ?>
    </div>
</section>
