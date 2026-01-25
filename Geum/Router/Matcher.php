<?php

namespace Geum\Router;

class Matcher
{
    /**
     * Match owned routes against the current request path.
     */
    public static function matchOwned(RouteCollection $routes, string $path): ?Route
    {
        foreach ($routes->getOwned() as $pattern => $route) {
            if (static::matchPattern($pattern, $path)) {
                return $route;
            }
        }

        return null;
    }

    /**
     * Match decorated routes against the current WordPress context.
     * Follows WP template hierarchy fallback pattern.
     */
    public static function matchDecorated(RouteCollection $routes): ?Route
    {
        $contexts = static::getCurrentContexts();
        $decorated = $routes->getDecorated();

        // Check each context in order (most specific first)
        foreach ($contexts as $context) {
            if (isset($decorated[$context])) {
                return $decorated[$context];
            }
        }

        return null;
    }

    /**
     * Match a URL pattern against a path.
     * Supports simple patterns with {param} placeholders.
     */
    protected static function matchPattern(string $pattern, string $path): bool
    {
        // Exact match
        if ($pattern === $path) {
            return true;
        }

        // Convert pattern to regex
        $regex = preg_replace('/\{[^}]+\}/', '([^/]+)', $pattern);
        $regex = '#^'.preg_quote($regex, '#').'$#';
        $regex = str_replace('\\(\\[\\^/\\]\\+\\)', '([^/]+)', $regex);

        return (bool) preg_match($regex, $path);
    }

    /**
     * Get current WordPress context(s) in fallback order.
     * Most specific first, following WP template hierarchy.
     */
    protected static function getCurrentContexts(): array
    {
        $contexts = [];

        if (\is_404()) {
            $contexts[] = '404';

            return $contexts;
        }

        if (\is_search()) {
            $contexts[] = 'search';

            return $contexts;
        }

        // Term archive (category, tag, custom taxonomy term)
        if (\is_tax() || \is_category() || \is_tag()) {
            $term = \get_queried_object();
            if ($term instanceof \WP_Term) {
                // Most specific: term:taxonomy:slug
                $contexts[] = "term:{$term->taxonomy}:{$term->slug}";
                // Fallback: taxonomy:taxonomy_name
                $contexts[] = "taxonomy:{$term->taxonomy}";

                // If taxonomy is associated with a post type, add that as fallback
                $taxonomy = \get_taxonomy($term->taxonomy);
                if ($taxonomy && ! empty($taxonomy->object_type)) {
                    foreach ($taxonomy->object_type as $postType) {
                        $contexts[] = "post_type:{$postType}";
                    }
                }
            }

            return $contexts;
        }

        // Post type archive
        if (\is_post_type_archive()) {
            $postType = \get_query_var('post_type');
            if (is_array($postType)) {
                $postType = reset($postType);
            }
            $contexts[] = "post_type:{$postType}";

            return $contexts;
        }

        // Blog home (posts archive)
        if (\is_home()) {
            $contexts[] = 'archive:post';
            $contexts[] = 'post_type:post';

            return $contexts;
        }

        // Date archive
        if (\is_date()) {
            $contexts[] = 'archive:date';

            return $contexts;
        }

        // Author archive
        if (\is_author()) {
            $contexts[] = 'archive:author';

            return $contexts;
        }

        return $contexts;
    }
}
