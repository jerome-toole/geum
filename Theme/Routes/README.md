# Routes

All application routing is defined in `routes.php`. This is the single source of truth for what URL or WordPress context maps to which controller.

## Two route types

**Owned routes** — custom URLs handled entirely by the theme:
```php
Router::route('/events/calendar', CalendarController::class);
```

**Decorated routes** — WordPress archives given a controller and slots:
```php
Router::decoratePostType('event', EventsModule::class)
    ->withPage('events')
    ->withSlot('template-content', [EventsModule::class, 'renderArchive']);
```

`withPage()` ensures a backing WP page exists (used for title, SEO, etc.).
`withSlot()` wires a named slot to a render callable — typically a static method on the module.

## Convention

- **Wiring lives here** — route registration belongs in `routes.php`, not in module `init()`.
- **Rendering lives in the module** — slot callables point to module or controller methods.
- **Handler `prepare()`** — if a controller needs to set up query state before rendering, implement `prepare()` on the handler class; it runs before any slot is rendered.
