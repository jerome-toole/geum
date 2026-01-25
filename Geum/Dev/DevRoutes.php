<?php

namespace Geum\Dev;

class DevRoutes
{
    public static function init()
    {
        if (\wp_get_environment_type() !== 'development') {
            return;
        }

        \add_action('init', [__CLASS__, 'addRewriteRules']);
        \add_filter('query_vars', [__CLASS__, 'addQueryVars']);
        \add_action('template_redirect', [__CLASS__, 'handleDevRoutes']);
        \add_filter('redirect_canonical', [__CLASS__, 'preventCanonicalRedirect'], 10, 2);
        \add_action('wp_head', [__CLASS__, 'injectDevStyles']);

        \add_action('after_switch_theme', [__CLASS__, 'flushRules']);
    }

    public static function injectDevStyles(): void
    {
        if (empty(\get_query_var('dev_route'))) {
            return;
        }

        ?>
        <meta name="robots" content="noindex, nofollow">
        <style>
            .component-example-section {
                margin-bottom: 3rem;
            }
            .component-example-section__title {
                font-size: 1.25rem;
                font-weight: 600;
                margin-bottom: 1rem;
                padding-bottom: 0.5rem;
                border-bottom: 1px solid #e5e5e5;
            }
            .component-example-section__description {
                color: #666;
                margin-bottom: 1.5rem;
            }
            .component-example-section__preview {
                padding: 2rem;
                background: #f9f9f9;
                border: 1px solid #e5e5e5;
                border-radius: 0.5rem;
            }
            .dev-component-example__back,
            .dev-component-list__back,
            .dev-index__back {
                display: inline-block;
                margin-bottom: 1rem;
                color: inherit;
                opacity: 0.7;
            }
            .dev-component-list__items {
                gap: 0.5rem;
            }
            .dev-component-list__items a {
                display: inline-block;
                padding: 0.5rem 1rem;
                background: #1a1a2e;
                color: #fff;
                text-decoration: none;
                border-radius: 0.25rem;
                font-size: 0.875rem;
            }
            .dev-component-list__items a:hover {
                background: #2a2a4e;
            }
            .dev-index__links {
                list-style: none;
                padding: 0;
            }
            .dev-index__links a {
                display: inline-block;
                padding: 0.75rem 1.5rem;
                background: #1a1a2e;
                color: #fff;
                text-decoration: none;
                border-radius: 0.25rem;
            }
        </style>
        <?php
    }

    public static function addRewriteRules(): void
    {
        \add_rewrite_rule(
            '^_dev/?$',
            'index.php?dev_route=index',
            'top'
        );

        \add_rewrite_rule(
            '^_dev/components/?$',
            'index.php?dev_route=components',
            'top'
        );

        \add_rewrite_rule(
            '^_dev/components/([^/]+)/?$',
            'index.php?dev_route=component&dev_component=$matches[1]',
            'top'
        );

        \add_rewrite_rule(
            '^_dev/globals/?$',
            'index.php?dev_route=globals',
            'top'
        );

        \add_rewrite_rule(
            '^_dev/utilities/?$',
            'index.php?dev_route=utilities',
            'top'
        );
    }

    public static function addQueryVars(array $vars): array
    {
        $vars[] = 'dev_route';
        $vars[] = 'dev_component';

        return $vars;
    }

    public static function preventCanonicalRedirect($redirect_url, $requested_url)
    {
        if (\get_query_var('dev_route')) {
            return false;
        }

        $path = parse_url($requested_url, PHP_URL_PATH);
        if ($path && preg_match('#^/_dev(/|$)#', $path)) {
            return false;
        }

        return $redirect_url;
    }

    public static function handleDevRoutes(): void
    {
        $dev_route = \get_query_var('dev_route');

        if (empty($dev_route)) {
            return;
        }

        \nocache_headers();

        switch ($dev_route) {
            case 'index':
                static::renderDevIndex();
                break;

            case 'components':
                static::renderComponentList();
                break;

            case 'component':
                $component = \sanitize_file_name(\get_query_var('dev_component'));
                static::renderComponentExample($component);
                break;

            case 'globals':
                static::renderGlobals();
                break;

            case 'utilities':
                static::renderUtilities();
                break;
        }

        exit;
    }

    protected static function renderDevIndex(): void
    {
        \get_header();
        \site_main_open();
        echo static::getDevIndexContent();
        \site_main_close();
        \get_footer();
    }

    protected static function renderComponentList(): void
    {
        \get_header();
        \site_main_open();
        echo static::getComponentListContent();
        \site_main_close();
        \get_footer();
    }

    protected static function renderComponentExample(string $component): void
    {
        $example_path = \get_theme_file_path("components/{$component}/example.php");

        if (! file_exists($example_path)) {
            \wp_die(
                \esc_html("No example found for component: {$component}"),
                'Component Not Found',
                ['response' => 404]
            );
        }

        \get_header();
        \site_main_open();

        $faker = class_exists('\Faker\Factory') ? \Faker\Factory::create() : null;

        ob_start();
        include $example_path;
        $example_output = ob_get_clean();

        echo '<div class="dev-component-example">';
        echo '<div class="dev-component-example__header">';
        echo '<a href="'.\esc_url(\home_url('/_dev/components/')).'" class="dev-component-example__back">&larr; All Components</a>';
        echo '<h1 class="dev-component-example__title">'.\esc_html(\ucwords(\str_replace('-', ' ', $component))).'</h1>';
        echo '</div>';
        echo '<div class="dev-component-example__content">'.$example_output.'</div>';
        echo '</div>';

        \site_main_close();
        \get_footer();
    }

    protected static function getDevIndexContent(): string
    {
        $content = '<div class="dev-index">';
        $content .= '<h1>Development Tools</h1>';
        $content .= '<ul class="dev-index__links flex-list">';
        $content .= '<li><a href="'.\esc_url(\home_url('/_dev/globals/')).'">Globals</a></li>';
        $content .= '<li><a href="'.\esc_url(\home_url('/_dev/utilities/')).'">Utilities</a></li>';
        $content .= '<li><a href="'.\esc_url(\home_url('/_dev/components/')).'">Components</a></li>';
        $content .= '</ul>';
        $content .= '</div>';

        return $content;
    }

    protected static function getComponentListContent(): string
    {
        $components = static::getComponentsWithExamples();

        $content = '<div class="dev-component-list">';
        $content .= '<div class="dev-component-list__header">';
        $content .= '<a href="'.\esc_url(\home_url('/_dev/')).'" class="dev-component-list__back">&larr; Dev Index</a>';
        $content .= '<h1>Components</h1>';
        $content .= '</div>';

        if (empty($components)) {
            $content .= '<p>No components with examples found. Add an <code>example.php</code> file to a component directory.</p>';
        } else {
            $content .= '<ul class="dev-component-list__items flex-list">';
            foreach ($components as $comp_name) {
                $url = \esc_url(\home_url("/_dev/components/{$comp_name}/"));
                $label = \esc_html(\ucwords(\str_replace('-', ' ', $comp_name)));
                $content .= "<li><a href=\"{$url}\">{$label}</a></li>";
            }
            $content .= '</ul>';
        }

        $content .= '</div>';

        return $content;
    }

    protected static function renderGlobals(): void
    {
        \get_header();
        \site_main_open();

        $colors = static::getThemeColors();

        echo '<a href="'.\esc_url(\home_url('/_dev/')).'" class="dev-component-list__back">&larr; Dev Index</a>';
        echo '<h1>Globals</h1>';

        $template_path = static::getTemplatePath('globals.php');
        if ($template_path) {
            include $template_path;
        }

        echo '<h2>HTML Elements</h2>';
        $template_path = static::getTemplatePath('html-elements.php');
        if ($template_path) {
            include $template_path;
        }

        \site_main_close();
        \get_footer();
    }

    protected static function renderUtilities(): void
    {
        \get_header();
        \site_main_open();

        echo '<div class="dev-nav"><a href="'.\esc_url(\home_url('/_dev/')).'" class="dev-component-list__back">&larr; Dev Index</a></div>';
        echo '<h1>Utilities</h1>';

        $template_path = static::getTemplatePath('utilities.php');
        if ($template_path) {
            include $template_path;
        }

        \site_main_close();
        \get_footer();
    }

    /**
     * Get template path with theme override support.
     * Checks Theme/Dev/templates/ first, falls back to Geum/Dev/templates/.
     */
    protected static function getTemplatePath(string $template): ?string
    {
        $theme_path = \get_theme_file_path("Theme/Dev/templates/{$template}");
        if (file_exists($theme_path)) {
            return $theme_path;
        }

        $framework_path = \get_theme_file_path("Geum/Dev/templates/{$template}");
        if (file_exists($framework_path)) {
            return $framework_path;
        }

        return null;
    }

    protected static function getComponentsWithExamples(): array
    {
        $examples = glob(\get_theme_file_path('components/*/example.php'));

        return array_map(function ($path) {
            return basename(dirname($path));
        }, $examples ?: []);
    }

    protected static function getThemeColors(): array
    {
        return \Geum\WordPress\Colors::getWordPressPalette();
    }

    public static function flushRules(): void
    {
        static::addRewriteRules();
        \flush_rewrite_rules();
    }
}
