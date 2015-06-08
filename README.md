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

1. `git clone http://...`
2. `ln -s apple-export /var/www/my-wp-installation/wp-content/plugins/apple-export`

## Running tests
You'll need PHPUnit v4.5+ to run tests. Initially you'll need to set up the
test environment, you can do so by running

    bin/install-wp-tests.sh <db-name> <db-user> <db-pass> [db-host] [wp-version]

For example

    bin/install-wp-tests.sh wp_test root '' localhost latest

That script will download latest wordpress as well as latest wordpress' tests so
`/tests/bootstrap.php` can load them.

Once the script is run, simply run the `phpunit` command and you're good to go!

