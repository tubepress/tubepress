<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of epilog (https://github.com/ehough/epilog)
 *
 * epilog is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * epilog is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

require_once __DIR__ . '/../../../vendor/ehough/pulsar/src/main/php/ehough/pulsar/ComposerClassLoader.php';

$loader = new ehough_pulsar_ComposerClassLoader(__DIR__ . '/../../../vendor/');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/core/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/addon/wordpress/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/addon/youtube/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/addon/vimeo/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/addon/basicplayerlocations/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/addon/embedplus/classes');
$loader->registerFallbackDirectory(__DIR__ . '/../../main/php/plugins/addon/jwplayer/classes');
$loader->registerFallbackDirectory(__DIR__);
$loader->register();

//TODO: remove this later.
define('TUBEPRESS_BOOTED', true);