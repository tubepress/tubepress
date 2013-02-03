<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

require_once __DIR__ . '/../../../vendor/ehough/pulsar/src/main/php/ehough/pulsar/ComposerClassLoader.php';

$loader = new ehough_pulsar_ComposerClassLoader(__DIR__ . '/../../../vendor/');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/core/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/wordpress/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/youtube/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/vimeo/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/basicplayerlocations/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/embedplus/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/jwplayer/classes');
$loader->registerFallbackDirectory(__DIR__);
$loader->register();

//TODO: remove this later.
define('TUBEPRESS_BOOTED', true);