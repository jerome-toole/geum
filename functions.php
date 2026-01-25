<?php

// ----------------------------------------------------
// Register the autoloader from Composer.
// ----------------------------------------------------

if (file_exists($autoloader = __DIR__.'/vendor/autoload.php')) {
    require $autoloader;
}

require __DIR__.'/Geum/functions.php';

// ----------------------------------------------------
// Load config values.
// ----------------------------------------------------
\Geum\Config::init();

// ----------------------------------------------------
// Load core framework functionality.
// ----------------------------------------------------
\Geum\Component::init();

\Geum\WordPress\Admin::init();
\Geum\WordPress\Cleanup::init();
\Geum\WordPress\Comments::init();
\Geum\WordPress\EditHomepage::init();
\Geum\WordPress\Emails::init();
\Geum\WordPress\Enqueue::init();
\Geum\WordPress\Escaping::init();
\Geum\WordPress\Gutenberg::init();
\Geum\WordPress\Colors::init();
\Geum\WordPress\Head::init();
\Geum\WordPress\Images::init();
\Geum\WordPress\PostsPT::init();
\Geum\WordPress\Security::init();
\Geum\WordPress\ThemeSetup::init();
\Geum\Router::init();
\Geum\WordPress\Updates::init();
\Geum\WordPress\UploadMimes::init();
\Geum\Dev\DevRoutes::init();

// ----------------------------------------------------
// Load Theme Modules.
// ----------------------------------------------------
\Geum\Module::init();

// ----------------------------------------------------
// Load Theme Utilities.
// ----------------------------------------------------
\Theme\Utils\YearShortcode::init();
