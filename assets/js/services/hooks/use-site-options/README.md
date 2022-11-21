# Custom Hooks: useSiteOptions
Get and set site options via `apiFetch`. Inherits user's capabilities and returns an error notice to the snackbar if the user is not able to fetch or set options.

Utilize also `notices` to return the snackbar messages.
## Usage
### Getting site settings

```jsx
const [{ loading, saving, settings }, setOptions] = useSiteOptions();
```

Utilize the settings object as the object containing settings available to the user.

### Setting site settings.
Expects the full settings object on save. Spread settings, and set the new key/value pair as the second param.

```jsx
(next) => setHolder({ ...settings, options_key: next })
```
