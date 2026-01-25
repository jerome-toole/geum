/**
 * @link https://css-tricks.com/a-better-api-for-the-resize-observer/
 *
 */
export function resizeObserver(node, options = {}) {
  const observer = new ResizeObserver(observerFn)
  const { callback, ...opts } = options

  function observerFn(entries) {
    for (const entry of entries) {
      // Callback pattern
      if (callback) callback({ entry, entries, observer })
      // Event listener pattern
      else {
        node.dispatchEvent(
          new CustomEvent('resize-obs', {
            detail: { entry, entries, observer },
          })
        )
      }
    }
  }

  observer.observe(node)

  return {
    unobserve(node) {
      observer.unobserve(node)
    },

    disconnect() {
      observer.disconnect()
    }
  }
}
