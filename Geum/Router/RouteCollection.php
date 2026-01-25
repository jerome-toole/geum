<?php

namespace Geum\Router;

class RouteCollection
{
    protected array $owned = [];

    protected array $decorated = [];

    public function add(Route $route): void
    {
        if ($route->getType() === 'owned') {
            $this->owned[$route->getPattern()] = $route;
        } else {
            $this->decorated[$route->getPattern()] = $route;
        }
    }

    public function getOwned(): array
    {
        return $this->owned;
    }

    public function getDecorated(): array
    {
        return $this->decorated;
    }

    public function findByRole(string $role): ?Route
    {
        foreach ($this->owned as $route) {
            if ($route->getRole() === $role) {
                return $route;
            }
        }

        foreach ($this->decorated as $route) {
            if ($route->getRole() === $role) {
                return $route;
            }
        }

        return null;
    }

    public function findByName(string $name): ?Route
    {
        foreach ($this->owned as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        foreach ($this->decorated as $route) {
            if ($route->getName() === $name) {
                return $route;
            }
        }

        return null;
    }
}
