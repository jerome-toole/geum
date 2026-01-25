# The Element Component
A generic component which can be used to replace echoing, concatenating, or otherwise manipulating strings to generate HTML. For example:
- Passing an HTML element into hooks or filters where a normal component wouldn't make sense (e.g. unnecessary overhead, a one-off situation, etc).
- As an 'abstract' component for simple components to extend with more specific arguments.
- As a simple wrapper for another component instead of multiple logic checks.
