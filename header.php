<!DOCTYPE html>
<html <?php language_attributes(); ?> class="no-js">
<head>
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
    <?php wp_body_open(); ?>

    <?= \Geum\Components\CookieConsent::make(); ?>
    <?= \Geum\Components\SkipLink::make(); ?>
    <?= \Geum\Components\SiteHeader::make(); ?>
