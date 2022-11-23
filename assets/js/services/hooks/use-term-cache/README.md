# Custom Hooks: useTermCache

Get and set terms from a cache for from various taxonomies.

## Usage

```jsx
const termCache = useTermCache();
const myTerm = termCache.get('category', 5);
termCache.set({ /* REST response here */ });
```

Returns the API response for the term ID in the given taxonomy. Caches it for
future use.
