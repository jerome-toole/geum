<?php

namespace Geum\Router;

/**
 * Migrates TemplatePage posts to Router Pages.
 *
 * Run via: Geum\Router\Migration::run()
 * Or via WP-CLI: wp eval "Geum\Router\Migration::run();"
 */
class Migration
{
    protected static array $log = [];

    /**
     * Run the migration.
     *
     * @param  bool  $dryRun  If true, don't make changes, just report what would happen.
     * @return array Migration log.
     */
    public static function run(bool $dryRun = false): array
    {
        static::$log = [];
        static::log('Starting migration'.($dryRun ? ' (dry run)' : ''));

        $templatePages = static::getTemplatePages();

        if (empty($templatePages)) {
            static::log('No template pages found to migrate.');

            return static::$log;
        }

        static::log(sprintf('Found %d template page(s) to migrate.', count($templatePages)));

        foreach ($templatePages as $templatePage) {
            static::migrateTemplatePage($templatePage, $dryRun);
        }

        static::log('Migration complete.');

        return static::$log;
    }

    /**
     * Get all geum-template posts.
     */
    protected static function getTemplatePages(): array
    {
        return \get_posts([
            'post_type' => 'geum-template',
            'post_status' => ['publish', 'draft', 'pending'],
            'posts_per_page' => -1,
            'orderby' => 'ID',
            'order' => 'ASC',
        ]);
    }

    /**
     * Migrate a single template page.
     */
    protected static function migrateTemplatePage(\WP_Post $templatePage, bool $dryRun): void
    {
        $objectData = \get_post_meta($templatePage->ID, 'geum-template-page-data', true);

        if (empty($objectData)) {
            static::log("  Skipping #{$templatePage->ID} '{$templatePage->post_title}': No object data.");

            return;
        }

        $role = static::mapToRole($objectData);

        if (! $role) {
            static::log("  Skipping #{$templatePage->ID} '{$templatePage->post_title}': Could not determine role.");

            return;
        }

        static::log("  Migrating #{$templatePage->ID} '{$templatePage->post_title}' → role: {$role}");

        // Check if router page already exists
        $existingPageId = \get_option("geum_router_page_{$role}");
        if ($existingPageId && \get_post($existingPageId)) {
            static::log("    Router page already exists (#{$existingPageId}). Skipping.");

            return;
        }

        if ($dryRun) {
            static::log('    Would create router page and update options.');

            return;
        }

        // Create router page
        $content = static::transformContent($templatePage->post_content, $objectData);

        $routerPageId = \wp_insert_post([
            'post_type' => 'page',
            'post_title' => $templatePage->post_title,
            'post_name' => $role,
            'post_status' => $templatePage->post_status,
            'post_content' => $content,
            'meta_input' => [
                '_is_router_page' => true,
                '_router_role' => $role,
                '_migrated_from_template_page' => $templatePage->ID,
            ],
        ]);

        if (! $routerPageId || \is_wp_error($routerPageId)) {
            static::log('    Failed to create router page.');

            return;
        }

        static::log("    Created router page #{$routerPageId}");

        // Update option to point to new router page
        \update_option("geum_router_page_{$role}", $routerPageId, false);
        static::log("    Updated option geum_router_page_{$role}");

        // Update old option to also point to new page (for backwards compat)
        static::updateLegacyOption($objectData, $routerPageId);
    }

    /**
     * Map TemplatePage object data to router role.
     */
    protected static function mapToRole(object $objectData): ?string
    {
        $type = $objectData->type ?? null;
        $id = $objectData->id ?? null;
        $slug = $objectData->slug ?? null;

        if ($type === 'wp_post_type') {
            // post → blog, others → post_type:{name}
            if ($id === 'post') {
                return 'blog';
            }

            return $slug ?: $id;
        }

        if ($type === 'wp_taxonomy') {
            return $slug ? "{$slug}-listing" : null;
        }

        if ($type === 'wp_term') {
            // Get term to find taxonomy
            $term = \get_term_by('term_taxonomy_id', $id);
            if ($term instanceof \WP_Term) {
                return "{$term->taxonomy}-{$term->slug}";
            }

            return null;
        }

        if ($type === '404') {
            return '404';
        }

        return null;
    }

    /**
     * Transform content from TemplatePage format to Router format.
     */
    protected static function transformContent(string $content, object $objectData): string
    {
        // Replace template-loop block with listing block
        $content = str_replace(
            '<!-- wp:acf/template-loop /-->',
            '<!-- wp:acf/listing /-->',
            $content
        );

        $content = str_replace(
            '<!-- wp:acf/template-loop -->',
            '<!-- wp:acf/listing -->',
            $content
        );

        // For 404 pages, use template-content block if empty
        if ($objectData->type === '404' && empty(trim($content))) {
            $content = '<!-- wp:acf/template-content /-->';
        }

        return $content;
    }

    /**
     * Update legacy option for backwards compatibility.
     */
    protected static function updateLegacyOption(object $objectData, int $routerPageId): void
    {
        $type = $objectData->type ?? null;
        $id = $objectData->id ?? null;
        $slug = $objectData->slug ?? null;

        if ($type === 'wp_post_type' || $type === 'wp_taxonomy') {
            if ($slug) {
                \update_option("{$slug}_template_page", $routerPageId, false);
                static::log("    Updated legacy option {$slug}_template_page");
            }
        } elseif ($type === 'wp_term') {
            \update_term_meta($id, 'template_page', $routerPageId);
            static::log("    Updated legacy term meta template_page for term {$id}");
        } elseif ($type === '404') {
            \update_option('404_template_page', $routerPageId, false);
            static::log('    Updated legacy option 404_template_page');
        }
    }

    /**
     * Log a message.
     */
    protected static function log(string $message): void
    {
        static::$log[] = $message;

        if (defined('WP_CLI') && WP_CLI) {
            \WP_CLI::log($message);
        }
    }

    /**
     * Delete old template pages after migration.
     * Run this only after verifying migration was successful.
     *
     * @param  bool  $dryRun  If true, don't delete, just report.
     */
    public static function cleanup(bool $dryRun = false): array
    {
        static::$log = [];
        static::log('Starting cleanup'.($dryRun ? ' (dry run)' : ''));

        $templatePages = static::getTemplatePages();

        if (empty($templatePages)) {
            static::log('No template pages found.');

            return static::$log;
        }

        foreach ($templatePages as $templatePage) {
            $migratedTo = null;

            // Check if this was migrated
            $objectData = \get_post_meta($templatePage->ID, 'geum-template-page-data', true);
            if ($objectData) {
                $role = static::mapToRole($objectData);
                if ($role) {
                    $routerPageId = \get_option("geum_router_page_{$role}");
                    $routerPage = $routerPageId ? \get_post($routerPageId) : null;

                    if ($routerPage) {
                        $migratedFrom = \get_post_meta($routerPage->ID, '_migrated_from_template_page', true);
                        if ((int) $migratedFrom === $templatePage->ID) {
                            $migratedTo = $routerPageId;
                        }
                    }
                }
            }

            if ($migratedTo) {
                static::log("  #{$templatePage->ID} '{$templatePage->post_title}' → migrated to #{$migratedTo}");

                if (! $dryRun) {
                    \wp_delete_post($templatePage->ID, true);
                    static::log('    Deleted.');
                }
            } else {
                static::log("  #{$templatePage->ID} '{$templatePage->post_title}' → NOT migrated, skipping.");
            }
        }

        static::log('Cleanup complete.');

        return static::$log;
    }
}
