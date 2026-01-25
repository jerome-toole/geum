<?php

namespace Geum;

use Geum\Router\Matcher;
use Geum\Router\Route;
use Geum\Router\RouteCollection;
use Geum\Router\RouterPage;
use Geum\Router\Slot;

class Router
{
    protected static RouteCollection $routes;

    protected static ?Route $current = null;

    public static function init(): void
    {
        static::$routes = new RouteCollection;

        // Load theme routes
        $routesFile = \get_theme_file_path('Theme/Routes/routes.php');
        if (file_exists($routesFile)) {
            require $routesFile;
        }

        // Hook into WordPress
        \add_action('parse_request', [static::class, 'matchOwnedRoutes'], 1);
        \add_action('template_redirect', [static::class, 'matchDecoratedRoutes'], 1);
        \add_filter('template_include', [static::class, 'resolveTemplate'], 999);

        // Initialize subsystems
        RouterPage::init();
        Slot::init();
    }

    /** @param string|callable $handler */
    public static function route(string $pattern, mixed $handler): Route
    {
        $route = new Route('owned', $pattern, $handler);
        static::$routes->add($route);

        return $route;
    }

    /** @param string|callable $handler */
    public static function decorate(string $target, mixed $handler): Route
    {
        $route = new Route('decorated', $target, $handler);
        static::$routes->add($route);

        return $route;
    }

    public static function ensurePage(string $role, array $attributes = []): void
    {
        RouterPage::ensure($role, $attributes);
    }

    public static function current(): ?Route
    {
        return static::$current;
    }

    public static function renderSlot(string $name): string
    {
        if (! static::$current) {
            return '';
        }

        $slot = static::$current->getSlot($name);

        return $slot ? call_user_func($slot) : '';
    }

    public static function getRouteByRole(string $role): ?Route
    {
        return static::$routes->findByRole($role);
    }

    /**
     * Get the router page for the current route.
     */
    public static function getPage(): ?\WP_Post
    {
        if (! static::$current) {
            return null;
        }

        $role = static::$current->getRole();
        if (! $role) {
            return null;
        }

        return RouterPage::getPageByRole($role);
    }

    public static function renderPage(): void
    {
        if (! static::$current) {
            return;
        }

        $role = static::$current->getRole();
        if (! $role) {
            return;
        }

        $pageId = \get_option("geum_router_page_{$role}");
        $page = $pageId ? \get_post($pageId) : null;

        if ($page && $page->post_status === 'publish') {
            echo \apply_filters('the_content', $page->post_content);
        }
    }

    public static function matchOwnedRoutes(\WP $wp): void
    {
        $path = '/'.trim($wp->request, '/');
        $route = Matcher::matchOwned(static::$routes, $path);

        if ($route) {
            static::$current = $route;
            static::dispatch($route);
            exit;
        }
    }

    public static function matchDecoratedRoutes(): void
    {
        $route = Matcher::matchDecorated(static::$routes);

        if ($route) {
            static::$current = $route;
        }
    }

    public static function resolveTemplate(string $template): string
    {
        if (! static::$current) {
            return $template;
        }

        // Handler prepares data
        static::$current->prepare();

        // Return resolved template path
        return static::$current->getTemplate() ?? $template;
    }

    protected static function dispatch(Route $route): void
    {
        $route->prepare();

        $handler = $route->getHandler();

        if (is_callable($handler)) {
            call_user_func($handler);
        } elseif (is_string($handler) && class_exists($handler)) {
            $instance = new $handler;
            if (method_exists($instance, 'handle')) {
                $instance->handle();
            }
        }

        // Load template if set
        $template = $route->getTemplate();
        if ($template && file_exists($template)) {
            include $template;
        }
    }

    public static function getRoutes(): RouteCollection
    {
        return static::$routes;
    }
}
