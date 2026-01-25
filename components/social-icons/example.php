<?php

/**
 * SocialIcons Component Examples
 *
 * Note: Requires social_networks ACF field to be populated, or pass networks directly.
 */

use Geum\Components\SocialIcons;

?>

<section class="component-example-section">
    <h2 class="component-example-section__title">Social Icons</h2>
    <p class="component-example-section__description">Social network icons from ACF settings.</p>
    <div class="component-example-section__preview">
        <?php
        // Try to render from ACF settings
        $result = SocialIcons::make();
if ($result) {
    echo $result;
} else {
    echo '<p><em>No social networks configured in ACF options. Add networks to see this example.</em></p>';
}
?>
    </div>
</section>

<section class="component-example-section">
    <h2 class="component-example-section__title">Social Icons with Title Template</h2>
    <p class="component-example-section__description">Icons with "Visit our %s page" format.</p>
    <div class="component-example-section__preview">
        <?php
$result = SocialIcons::make(title: 'Visit our %s page');
if ($result) {
    echo $result;
} else {
    echo '<p><em>No social networks configured.</em></p>';
}
?>
    </div>
</section>
