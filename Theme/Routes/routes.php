<?php

/**
 * Application Routes
 *
 * Define routes using:
 * - Router::route() for owned routes (custom URLs)
 * - Router::decorate() for decorated routes (WordPress archives)
 *
 * @see agents/2026-01-08-application-routing-system-pages.md
 */

use Geum\Router;
use Theme\Controllers\ArchiveController;
use Theme\Controllers\NotFoundController;
use Theme\Controllers\SearchController;

// Blog archive (posts)
Router::decorate('archive:post', ArchiveController::class)
    ->withContent('blog')
    ->withSlot('listing', fn () => ArchiveController::renderLoop());

// Search results
Router::decorate('search', SearchController::class)
    ->withContent('search')
    ->withSlot('listing', fn () => SearchController::renderResults());

// 404 page
Router::decorate('404', NotFoundController::class)
    ->withContent('404')
    ->withSlot('template-content', fn () => NotFoundController::renderContent());

// Example: Custom post type archive (uncomment to use)
Router::decorate('post_type:event', ArchiveController::class)
    ->withContent('events')
    ->withSlot('listing', fn () => ArchiveController::renderLoop());

// Example: Taxonomy archive (uncomment to use)
// Router::decorate('taxonomy:category', ArchiveController::class)
//     ->withContent('category-listing')
//     ->withSlot('listing', fn () => ArchiveController::renderLoop());
