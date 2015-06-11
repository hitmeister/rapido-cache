#!/bin/sh

set -e

echo "PHP Version: ${TRAVIS_PHP_VERSION}"
phpenv config-add tests/travis/php_extensions.ini
