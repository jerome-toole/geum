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

Use Playwright MCP for browser-based testing.

### 1. Load the Page

```
mcp__playwright__browser_navigate to http://geum.test
```

### 2. Check Console for Errors

```
mcp__playwright__browser_console_messages with level: "error"
```

Or include warnings:
```
mcp__playwright__browser_console_messages with level: "warning"
```

### 3. Inspect the DOM

Take a snapshot to see the page structure:
```
mcp__playwright__browser_snapshot
```

### 4. Test Interactions

Click elements:
```
mcp__playwright__browser_click on element with ref from snapshot
```

### 5. Take Screenshots

```
mcp__playwright__browser_take_screenshot
```

### 6. Clean Up

```
mcp__playwright__browser_close
```

## Full Test Workflow

When asked to "test" after making changes:

1. **Clear debug log**: `> ../../debug.log`
2. **Navigate with Playwright**: `mcp__playwright__browser_navigate` to the site
3. **Take snapshot**: Check DOM rendered correctly
4. **Check console**: `mcp__playwright__browser_console_messages` for JS errors
5. **Check PHP log**: `cat ../../debug.log` for backend errors
6. **Report results**: Summarize any issues found

## Testing Specific Components

If testing a specific component:

1. Find a page that uses the component
2. Load that page with Playwright
3. Use `mcp__playwright__browser_snapshot` to verify the component renders
4. Check console and debug.log for errors

## Network Requests

Check what requests were made:
```
mcp__playwright__browser_network_requests
```

## Common Issues

### Page returns 500
Check debug.log immediately - usually a PHP fatal error.

### Component not rendering
1. Check if component class exists and is namespaced correctly
2. Check template.php exists in component directory
3. Look for "Class not found" in debug.log

### JavaScript not working
1. Check `mcp__playwright__browser_console_messages` for errors
2. Verify Vite dev server is running (`npm run dev`)
3. Check network tab for failed script loads
