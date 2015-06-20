# Apple Export WordPress Plugin
Apple export plugin for WordPress. This plugin works only in the admin-side so
no `public/` folder.

## Coding standards
PHP code must follow WordPress' [Coding
Standards](https://codex.wordpress.org/WordPress_Coding_Standards).

Using [EditorConfig](http://editorconfig.org) is reccommended so files always
use the same formatting.

## Requirements
In order to work on the plugin, you'll need a webserver such as Apache with
PHP 5.3+ and MySQL 5+.

It's recommended to create a symbolic link for better code organization.

1. `git clone ssh://... wppl`
2. `cd wppl`
3. `ln -s /home/my-user/my-projects/wppl /var/www/my-wp-installation/wp-content/plugins/apple-export`

Make sure `/home/my-user` has execute permissions or WordPress might not show it
as a plugin. You can do so by doing `chmod o+x /home/my-user`.

## Running tests
You'll need PHPUnit v4.5+ to run tests. Initially you'll need to set up the
test environment, you can do so by running

    bin/install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]

For example

    bin/install-wp-tests.sh wp_test root '' localhost latest

That script will download latest wordpress as well as latest wordpress' tests so
`/tests/bootstrap.php` can load them.

You'll also need to set up your API credentials using your system's
environmental variables. For UNIX systems (OSX, Linux, etc) you can simply edit
the `~/.profile` file, adding your variables as follows:

    export WP_PLUGIN_KEY=my-api-key
    export WP_PLUGIN_SECRET=my-api-secret
    export WP_PLUGIN_CHANNEL=my-api-channel

Once updated, reload it using `source ~/.profile`. Test it out by doing `echo
$WP_PLUGIN_KEY`, it should display your key in the terminal.

Now you can simply use the `phpunit` command to run the tests.
