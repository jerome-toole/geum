<?php

namespace Geum;

class Asset
{
    /**
     * Takes an asset path and returns the compiled asset path from the Vite manifest.
     *
     * @param  string  $asset  A path to an asset (e.g., 'main.css', 'main.js').
     * @return string The compiled asset path, or an empty string on failure.
     */
    public static function extract(string $asset): string
    {
        $viteManifestPath = \Geum\Asset::path('build/manifest.json');

        if (! file_exists($viteManifestPath)) {
            \Geum\Helpers::errorLog('Vite manifest not found: '.$viteManifestPath);

            return '';
        }

        return self::extractFromViteManifest($asset);
    }

    /**
     * Extract asset path from Vite manifest.
     *
     * Vite manifest format: {"assets/main.js": {"file": "assets/main.js", ...}}
     *
     * @param  string  $asset  Asset filename (e.g., 'main.js', 'main-styles.css')
     * @return string The compiled asset path, or an empty string on failure.
     */
    private static function extractFromViteManifest(string $asset): string
    {
        $manifest = \Geum\Asset::decodedContent('build/manifest.json');

        if (empty($manifest)) {
            return '';
        }

        // Search through manifest entries to find matching output file
        foreach ($manifest as $entry) {
            if (! isset($entry['file'])) {
                continue;
            }

            $filename = basename($entry['file']);

            // Direct filename match
            if ($filename === $asset) {
                return $entry['file'];
            }

            // Check names array (Vite outputs e.g. names: ["editor-styles.css"])
            if (isset($entry['names']) && in_array($asset, $entry['names'], true)) {
                return $entry['file'];
            }

            // Handle name variations (e.g., 'main.css' maps to 'main-styles.css')
            if (self::isAssetMatch($asset, $filename)) {
                return $entry['file'];
            }
        }

        return '';
    }

    /**
     * Check if requested asset matches output filename.
     *
     * Handles naming variations in Vite output.
     *
     * @param  string  $requested  Requested asset name (e.g., 'main.css')
     * @param  string  $actual  Actual output filename (e.g., 'main-styles.css')
     */
    private static function isAssetMatch(string $requested, string $actual): bool
    {
        // Handle main.css → main-styles.css mapping
        $mappings = [
            'main.css' => 'main-styles.css',
            'editor.css' => 'editor-styles.css',
        ];

        if (isset($mappings[$requested]) && $mappings[$requested] === $actual) {
            return true;
        }

        return false;
    }

    /**
     * Retrieves the content of the asset at the given path.
     *
     * @param  string  $asset  A file path to an asset, relative to the theme root.
     * @return string The asset's content, or an empty string on failure.
     */
    public static function content(string $asset): string
    {
        $path = \Geum\Asset::path($asset);

        if (empty($path) || ! file_exists($path)) {
            return '';
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            return '';
        }

        return trim($contents);
    }

    /**
     * Retrieves the json_decoded content of the asset at the given path.
     *
     * @param  string  $asset  A file path to an asset, relative to the theme root.
     * @return string The asset's content as an associative array, or an empty array on failure.
     */
    public static function decodedContent(string $asset): array
    {
        $content = \Geum\Asset::content($asset);

        if (empty($content)) {
            return [];
        }

        return json_decode($content, true);
    }

    /**
     * Takes an uncompiled asset path and returns the full compiled theme path.
     *
     * @param  string  $asset  A path to an asset.
     * @param  bool  $manifest  Whether to use the manifest.json to return a versioned asset name.
     * @return string The full compiled theme asset path, or an empty string on failure.
     */
    public static function path(string $asset = '', bool $manifest = false): string
    {
        if ($manifest === true) {
            $asset = \Geum\Asset::extract($asset);
        }

        if (empty($asset)) {
            return '';
        }

        return \Geum\Paths::assetPath($asset);
    }

    /**
     * Takes an uncompiled asset path and returns the full compiled theme URL.
     *
     * @param  string  $asset  A path to an asset
     * @param  bool  $manifest  Whether to use the manifest.json to return a versioned asset name.
     * @return string The full compiled theme asset URL, or an empty string on failure.
     */
    public static function URL(string $asset, bool $manifest = false): string
    {
        // Map asset name to source path (e.g., 'main.js' -> 'assets/main.js')
        $sourcePath = \Geum\Vite::getSourcePath($asset);

        // Try Vite first (handles both dev server and production)
        $viteUrl = \Geum\Vite::asset($sourcePath);
        if ($viteUrl) {
            return $viteUrl;
        }

        // Fallback: legacy behavior for non-Vite assets
        if ($manifest === true) {
            $asset = \Geum\Asset::extract($asset);
        }

        if (empty($asset)) {
            return '';
        }

        return \Geum\Paths::assetURL($asset);
    }
}
