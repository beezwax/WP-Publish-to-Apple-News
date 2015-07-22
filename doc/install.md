# Install Apple News
Installation is similar to all other plugins for WordPress:

 1. Upload the entire `apple-news` folder to the `./wp-content/plugins`
    directory in your web server.
 2. Active the plugin though the 'Plugins' menu in WordPress.

 You will now find 'Apple News' in your WordPress admin panel. For usage
 documentation see [Usage](usage.md).

## Permissions Issues
Depending on your web server configuration, you might need to change the
permissions of the `./apple-export/workspace` and `./apple-export/workspace/tmp`
directories. The permissions vary depending on the configuration, nevertheless,
`775` permission will work on most setups.
