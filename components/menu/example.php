<?php

/**
 * Menu Component Examples
 *
 * Note: Requires a registered nav menu with has_nav_menu() returning true.
 */

use Geum\Components\Menu;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Header Menu</h2>
    <p class="component-example-section__description">Menu from 'header' theme location.</p>
    <div class="component-example-section__preview">
        <?php
        $result = Menu::make(theme_location: 'header');
if ($result) {
    echo $result;
} else {
    echo '<p><em>No menu assigned to "header" location.</em></p>';
}
?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Footer Menu with Heading</h2>
    <p class="component-example-section__description">Menu with auto-generated heading from menu name.</p>
    <div class="component-example-section__preview">
        <?php
$result = Menu::make(theme_location: 'footer-1', heading: true);
if ($result) {
    echo $result;
} else {
    echo '<p><em>No menu assigned to "footer-1" location.</em></p>';
}
?>
    </div>
</section>
