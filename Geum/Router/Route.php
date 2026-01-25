<?php

namespace Geum\Router;

use Geum\Router;

class Route
{
    protected string $type;

    protected string $pattern;

    /** @var string|callable */
    protected mixed $handler;

    protected ?string $role = null;

    protected ?string $template = null;

    protected array $slots = [];

    protected array $middleware = [];

    protected ?string $name = null;

    /** @param string|callable $handler */
    public function __construct(string $type, string $pattern, mixed $handler)
    {
        $this->type = $type;
        $this->pattern = $pattern;
        $this->handler = $handler;
    }

    public function withContent(string $role): static
    {
        $this->role = $role;
        Router::ensurePage($role);

        return $this;
    }

    public function noContent(): static
    {
        $this->role = null;

        return $this;
    }

    public function withSlot(string $name, callable $renderer): static
    {
        $this->slots[$name] = $renderer;

        return $this;
    }

    public function name(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function middleware(callable $fn): static
    {
        $this->middleware[] = $fn;

        return $this;
    }

    public function template(string $name): static
    {
        $this->template = $name;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getPattern(): string
    {
        return $this->pattern;
    }

    /** @return string|callable */
    public function getHandler(): mixed
    {
        return $this->handler;
    }

    public function getTemplate(): ?string
    {
        if ($this->template) {
            return \locate_template($this->template.'.php') ?: null;
        }

        return $this->resolveDefaultTemplate();
    }

    protected function resolveDefaultTemplate(): ?string
    {
        if ($this->type !== 'decorated') {
            return \locate_template('page.php') ?: \locate_template('index.php') ?: null;
        }

        $parts = explode(':', $this->pattern);
        $type = $parts[0];
        $name = $parts[1] ?? null;

        $templates = match ($type) {
            'post_type' => ["archive-{$name}.php", 'archive.php'],
            'taxonomy' => ["taxonomy-{$name}.php", 'taxonomy.php', 'archive.php'],
            'archive' => $name === 'post' ? ['home.php', 'index.php'] : ["archive-{$name}.php"],
            'search' => ['search.php', 'index.php'],
            '404' => ['404.php', 'index.php'],
            default => ['index.php'],
        };

        foreach ($templates as $t) {
            if ($path = \locate_template($t)) {
                return $path;
            }
        }

        return \locate_template('index.php') ?: null;
    }

    public function prepare(): void
    {
        // Run middleware
        foreach ($this->middleware as $fn) {
            call_user_func($fn, $this);
        }

        // Call handler's prepare if class-based
        if (is_string($this->handler) && class_exists($this->handler)) {
            $instance = new $this->handler;
            if (method_exists($instance, 'prepare')) {
                $instance->prepare();
            }
        }
    }

    public function getSlot(string $name): ?callable
    {
        return $this->slots[$name] ?? null;
    }

    public function getSlots(): array
    {
        return $this->slots;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getUrl(): string
    {
        if ($this->type === 'owned') {
            return \home_url($this->pattern);
        }

        // Decorated routes - resolve from WP
        $parts = explode(':', $this->pattern);
        $type = $parts[0];
        $name = $parts[1] ?? null;

        return match ($type) {
            'post_type' => \get_post_type_archive_link($name) ?: \home_url(),
            'taxonomy' => \home_url(), // Taxonomy archives need term context
            'archive' => $name === 'post'
                ? (\get_permalink(\get_option('page_for_posts')) ?: \home_url())
                : (\get_post_type_archive_link($name) ?: \home_url()),
            'search' => \home_url('?s='),
            '404' => \home_url(),
            default => \home_url(),
        };
    }
}
