<?php

/**
 * AnnouncementBanner Component Examples
 *
 * Note: Must have show=true to render. Uses ACF fields for fallback values.
 */

use Geum\Components\AnnouncementBanner;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Announcement Banner</h2>
    <p class="component-example-section__description">Site-wide announcement message.</p>
    <div class="component-example-section__preview">
        <?= AnnouncementBanner::make(
            show: true,
            message: 'Free shipping on all orders over $50! <a href="/shop">Shop now</a>',
        ); ?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Announcement with Background Color</h2>
    <p class="component-example-section__description">Styled announcement with theme color.</p>
    <div class="component-example-section__preview">
        <?= AnnouncementBanner::make(
            show: true,
            message: 'New products arriving soon - stay tuned!',
            background_color: 'brand-1',
        ); ?>
    </div>
</section>
