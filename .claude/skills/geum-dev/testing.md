# Testing Workflow

Test PHP backend and frontend changes on the Geum WordPress site.

## Quick Test

For a quick sanity check after making changes:

```bash
# Clear log, load page, check for errors
: > ../../debug.log && curl -sL -o /dev/null -w "%{http_code}" http://geum.test && cat ../../debug.log
```

Expected: HTTP 200, empty log output.

## PHP Testing

### 1. Clear and Monitor Debug Log

```bash
# Clear the log
: > ../../debug.log

# Or tail it live while testing
tail -f ../../debug.log
```

### 2. Trigger the Page

```bash
# Homepage
curl -sL -o /dev/null -w "%{http_code}\n" http://geum.test

# Specific page
curl -sL -o /dev/null -w "%{http_code}\n" http://geum.test/some-page/

# Follow redirects and show final URL
curl -sL -w "%{url_effective}\n" -o /dev/null http://geum.test
```

### 3. Check for Errors

```bash
# Show last 50 lines
tail -50 ../../debug.log

# Search for specific error types
grep -E "(Fatal|Error|Warning|Notice)" ../../debug.log

# Show errors with context
grep -B2 -A5 "Fatal error" ../../debug.log
```

### Common PHP Error Patterns

| Error | Likely Cause |
|-------|--------------|
| `Failed to open stream` | Missing file, check path |
| `Class not found` | Autoloading issue, check namespace |
| `Undefined variable` | Variable not set in scope |
| `Call to undefined method` | Method doesn't exist on class |

## Frontend Testing

Use Chrome DevTools MCP for browser-based testing.

### 1. Load the Page

```
mcp__chrome-devtools__navigate_page with url: "http://geum.test"
```

Or navigate to a specific page:
```
mcp__chrome-devtools__navigate_page with url: "http://geum.test/some-page/"
```

### 2. Check Console for Errors

```
mcp__chrome-devtools__list_console_messages
```

Filter by error type:
```
mcp__chrome-devtools__list_console_messages with types: ["error", "warn"]
```

Get details on a specific message:
```
mcp__chrome-devtools__get_console_message with msgid: <id>
```

### 3. Inspect the DOM

Take a snapshot to see the page structure (a11y tree):
```
mcp__chrome-devtools__take_snapshot
```

### 4. Test Interactions

Click elements (use uid from snapshot):
```
mcp__chrome-devtools__click with uid: "<uid>"
```

Fill form fields:
```
mcp__chrome-devtools__fill with uid: "<uid>", value: "text"
```

### 5. Take Screenshots

```
mcp__chrome-devtools__take_screenshot
```

Screenshot a specific element:
```
mcp__chrome-devtools__take_screenshot with uid: "<uid>"
```

### 6. Check Network Requests

```
mcp__chrome-devtools__list_network_requests
```

Get request details:
```
mcp__chrome-devtools__get_network_request with reqid: <id>
```

## Full Test Workflow

When asked to "test" after making changes:

1. **Clear debug log**: `: > ../../debug.log`
2. **Navigate**: `mcp__chrome-devtools__navigate_page` to the site
3. **Take snapshot**: `mcp__chrome-devtools__take_snapshot` to check DOM
4. **Check console**: `mcp__chrome-devtools__list_console_messages` for JS errors
5. **Check PHP log**: `cat ../../debug.log` for backend errors
6. **Report results**: Summarize any issues found

## Testing Specific Components

If testing a specific component:

1. Find a page that uses the component (or use `/_dev/` testing suite)
2. Load that page: `mcp__chrome-devtools__navigate_page`
3. Take snapshot: `mcp__chrome-devtools__take_snapshot` to verify component renders
4. Check console and debug.log for errors

## Performance Testing

Start a performance trace:
```
mcp__chrome-devtools__performance_start_trace with reload: true, autoStop: true
```

Analyze insights:
```
mcp__chrome-devtools__performance_analyze_insight
```

## Common Issues

### Page returns 500
Check debug.log immediately - usually a PHP fatal error.

### Component not rendering
1. Check if component class exists and is namespaced correctly
2. Check template.php exists in component directory
3. Look for "Class not found" in debug.log

### JavaScript not working
1. Check `mcp__chrome-devtools__list_console_messages` for errors
2. Verify Vite dev server is running (`npm run dev`)
3. Check `mcp__chrome-devtools__list_network_requests` for failed script loads

### Styles not applying
1. Run `npm run build` to rebuild assets
2. Check `mcp__chrome-devtools__list_network_requests` for CSS 404s
3. Take snapshot to verify classes are on elements
