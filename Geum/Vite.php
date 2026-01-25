<?php

namespace Geum;

/**
 * Laravel-style Vite integration for WordPress
 *
 * Handles asset loading with HMR support using the hot file approach.
 */
class Vite
{
    /**
     * Check if Vite dev server is running by looking for hot file.
     */
    public static function isRunning(): bool
    {
        if (\wp_get_environment_type() !== 'development') {
            return false;
        }

        $hotFile = \Geum\Paths::assetPath('hot');

        return file_exists($hotFile);
    }

    /**
     * Get the dev server URL from hot file.
     */
    public static function getDevServerUrl(): ?string
    {
        if (! self::isRunning()) {
            return null;
        }

        $hotFile = \Geum\Paths::assetPath('hot');
        $url = trim(file_get_contents($hotFile));

        // Normalize IPv6 localhost to avoid mixed content issues on HTTPS sites
        $url = str_replace(['http://[::1]', 'http://[::]:'], 'http://localhost', $url);

        return $url ?: 'http://localhost:5173';
    }

    /**
     * Generate Vite asset tags for given entry points.
     *
     * @param  array  $entryPoints  Entry point paths (e.g., ['assets/main.js', 'assets/main.pcss'])
     * @return string HTML tags for scripts and styles
     */
    public static function assets(array $entryPoints): string
    {
        $html = '';

        if (self::isRunning()) {
            $html .= self::makeDevServerTags($entryPoints);
        } else {
            $html .= self::makeProductionTags($entryPoints);
        }

        return $html;
    }

    /**
     * Generate tags for dev server (HMR).
     */
    private static function makeDevServerTags(array $entryPoints): string
    {
        $devServerUrl = self::getDevServerUrl();
        $html = '';

        // Inject Vite client for HMR
        $html .= sprintf(
            '<script type="module" src="%s/@vite/client"></script>'."\n",
            esc_url($devServerUrl)
        );

        // Add entry points
        foreach ($entryPoints as $entry) {
            $url = $devServerUrl.'/'.$entry;

            if (self::isCssPath($entry)) {
                $html .= sprintf(
                    '<link rel="stylesheet" href="%s" />'."\n",
                    esc_url($url)
                );
            } else {
                $html .= sprintf(
                    '<script type="module" src="%s"></script>'."\n",
                    esc_url($url)
                );
            }
        }

        return $html;
    }

    /**
     * Generate tags for production (built assets).
     */
    private static function makeProductionTags(array $entryPoints): string
    {
        $manifest = self::getManifest();
        if (! $manifest) {
            return '';
        }

        $html = '';
        $preloads = [];

        foreach ($entryPoints as $entry) {
            if (! isset($manifest[$entry])) {
                continue;
            }

            $manifestEntry = $manifest[$entry];
            $url = \Geum\Paths::assetURL('build/'.$manifestEntry['file']);

            if (self::isCssPath($entry)) {
                $html .= sprintf(
                    '<link rel="stylesheet" href="%s" />'."\n",
                    esc_url($url)
                );
            } else {
                $html .= sprintf(
                    '<script type="module" src="%s"></script>'."\n",
                    esc_url($url)
                );

                // Preload imports
                if (isset($manifestEntry['imports'])) {
                    foreach ($manifestEntry['imports'] as $import) {
                        if (isset($manifest[$import])) {
                            $importUrl = \Geum\Paths::assetURL('build/'.$manifest[$import]['file']);
                            $preloads[] = sprintf(
                                '<link rel="modulepreload" href="%s" />',
                                esc_url($importUrl)
                            );
                        }
                    }
                }

                // Preload CSS
                if (isset($manifestEntry['css'])) {
                    foreach ($manifestEntry['css'] as $css) {
                        $cssUrl = \Geum\Paths::assetURL('build/'.$css);
                        $html .= sprintf(
                            '<link rel="stylesheet" href="%s" />'."\n",
                            esc_url($cssUrl)
                        );
                    }
                }
            }
        }

        // Add preloads at the beginning
        if (! empty($preloads)) {
            $html = implode("\n", $preloads)."\n".$html;
        }

        return $html;
    }

    /**
     * Get the Vite manifest.
     */
    private static function getManifest(): ?array
    {
        $manifestPath = \Geum\Paths::assetPath('build/manifest.json');

        if (! file_exists($manifestPath)) {
            return null;
        }

        $content = file_get_contents($manifestPath);
        $manifest = json_decode($content, true);

        return $manifest ?: null;
    }

    /**
     * Get source path from asset name.
     *
     * Maps known asset names to their source paths. For unknown assets
     * (like component assets), returns the path as-is.
     *
     * @param  string  $asset  Asset name or path (e.g., 'main.js' or 'assets/components/header/scripts-main.js')
     * @return string Source path (e.g., 'assets/main.js')
     */
    public static function getSourcePath(string $asset): string
    {
        // Known main asset mappings
        $mappings = [
            'main.js' => 'assets/main.js',
            'main.css' => 'assets/main.pcss',
            'main-styles.css' => 'assets/main.pcss',
            'editor-scripts.js' => 'assets/editor-scripts.js',
            'editor-styles.css' => 'assets/editor-styles.pcss',
            'admin-scripts.js' => 'assets/admin-scripts.js',
        ];

        // Return mapped path if it exists, otherwise return asset as-is
        // This allows component assets to be passed through directly
        return $mappings[$asset] ?? $asset;
    }

    /**
     * Check if path is a CSS file.
     */
    private static function isCssPath(string $path): bool
    {
        return str_ends_with($path, '.css') || str_ends_with($path, '.pcss');
    }

    /**
     * Get asset URL for a given entry point or static asset.
     *
     * @param  string  $entry  Entry point path (e.g., 'assets/main.js') or static asset path (e.g., 'images/logo.svg')
     */
    public static function asset(string $entry): ?string
    {
        if (self::isRunning()) {
            return self::getDevServerUrl().'/'.$entry;
        }

        // Static assets (from assets/static/) don't have 'assets/' prefix
        // They're copied directly to public/build/ by Vite
        if (! str_starts_with($entry, 'assets/')) {
            return \Geum\Paths::assetURL('build/'.$entry);
        }

        // Compiled assets are in the manifest
        $manifest = self::getManifest();
        if ($manifest && isset($manifest[$entry])) {
            return \Geum\Paths::assetURL('build/'.$manifest[$entry]['file']);
        }

        return null;
    }
}
