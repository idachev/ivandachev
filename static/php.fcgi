#!/bin/bash

PHP_INI_SCAN_DIR=/home/lz2glco/.sh.phpmanager/php74.d
export PHP_INI_SCAN_DIR

DEFAULTPHPINI=/home/lz2glco/addon/ivandachev.com/php74-fcgi.ini
exec /opt/cpanel/ea-php74/root/usr/bin/php-cgi -c ${DEFAULTPHPINI}
