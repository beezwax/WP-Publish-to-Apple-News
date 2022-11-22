# Custom Hooks: useTaxonomies

Get and cache taxonomies config via `apiFetch`.

## Usage

```jsx
const taxonomies = useTaxonomies();
```

Returns the API response from /wp/v2/taxonomies. Caches it for future use.
