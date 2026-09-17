#!/bin/sh
set -e

php-fpm7.4 -F

exec "$@"