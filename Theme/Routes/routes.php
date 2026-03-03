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
use Theme\Controllers\NotFoundController;
use Theme\Controllers\SearchController;
use Theme\Modules\Events\EventsModule;

// Search results
Router::decorateSearch(SearchController::class)
    ->withPage('search')
    ->withSlot('template-content', fn () => SearchController::renderResults());

// 404 page
Router::decorate404(NotFoundController::class)
    ->withPage('404')
    ->withSlot('template-content', fn () => NotFoundController::renderContent());

// Events archive
Router::decoratePostType('event', EventsModule::class)
    ->withPage('events')
    ->withSlot('template-content', [EventsModule::class, 'renderArchive']);

// Example: Taxonomy archive for 'category' taxonomy
// Router::decorateTaxonomy('category', ArchiveController::class)
//     ->withPage('category-listing')
//     ->withSlot('template-content', fn () => ArchiveController::renderLoop());
