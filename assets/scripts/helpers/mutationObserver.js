/**
 * Sets up a MutationObserver on a given node.
 *
 * @link https://css-tricks.com/a-better-api-for-the-intersection-and-mutation-observers/
 *
 * @example Using callback pattern
 * mutationObserver(targetNode, {
 *  childList: true,
 *  callback({ entry, entries, observer }) {
 *   console.log('Mutation observed:', entry);
 *  }
 * });
 *
 * @example Using event listener pattern
 * const observer = mutationObserver(targetNode, { childList: true });
 * targetNode.addEventListener('mutate', (event) => {
 *   const { entry } = event.detail;
 *  console.log('Mutation observed:', entry);
 * });
 *
 */
export function mutationObserver(node, options = {}) {
  const observer = new MutationObserver(observerFn)
  const { callback, ...opts } = options
  observer.observe(node, opts)

  function observerFn(entries) {
    for (const entry of entries) {
      // Callback pattern
      if (options.callback) options.callback({ entry, entries, observer })
      // Event listener pattern
      else {
        node.dispatchEvent(
          new CustomEvent('mutate', {
            detail: { entry, entries, observer },
          })
        )
      }
    }
  }

  return {
    disconnect() {
      const records = observer.takeRecords()
      observer.disconnect()
      if (records.length > 0) observerFn(records)
    }
  }
}
