#!/usr/bin/env bash
#
# Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
#
# This file is part of TubePress (http://tubepress.com)
#
# This Source Code Form is subject to the terms of the Mozilla Public
# License, v. 2.0. If a copy of the MPL was not distributed with this
# file, You can obtain one at http://mozilla.org/MPL/2.0/.
#

composer install --prefer-source

MAJOR_PHP_VERSION=`php --version | head -n 1 | cut -d " " -f 2 | cut -d . -f 1`
MAJOR_PHP_VERSION=$(( MAJOR_PHP_VERSION ))

if [ "$MAJOR_PHP_VERSION" != "5" ]; then

    echo "Upgrading PHPUnit to 5.2. First removing version 4.8"
    composer remove  --dev phpunit/phpunit

    echo "Done removing PHPUnit 4.8. Now installing PHPUnit 5.2"
    composer require --dev phpunit/phpunit ^5.2

    echo "Done installing PHPUnit 5.2"
fi