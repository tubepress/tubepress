<?php
/**
Plugin Name: TubePress
Plugin URI: http://tubepress.com
Description: Displays gorgeous YouTube and Vimeo galleries in your posts, pages, and widgets. Upgrade to <a href="http://tubepress.com/pricing/">TubePress Pro</a> for more features!
Author: TubePress LLC
Version: 99.99.99
Author URI: http://tubepress.com

Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)

This file is part of TubePress (http://tubepress.com)

This Source Code Form is subject to the terms of the Mozilla Public
License, v. 2.0. If a copy of the MPL was not distributed with this
file, You can obtain one at http://mozilla.org/MPL/2.0/.
*/

/*
 * Tell TubePress where we're located.
 */
define('TUBEPRESS_ROOT', __DIR__);

/*
 * Boot TubePress and get the service container. On a properly-tuned server this should
 * take < 5 ms.
 *
 * @var tubepress_api_ioc_ContainerInterface
 */
/* @noinspection PhpIncludeInspection */
$serviceContainer = require TUBEPRESS_ROOT . '/src/php/scripts/boot.php';

/*
 * Get the WordPress entry point service.
 *
 * @var tubepress_wordpress_impl_EntryPoint
 */
$entryPoint = $serviceContainer->get('tubepress_wordpress_impl_EntryPoint');

/*
 * This integrates TubePress with WordPress filters, hooks, activation hooks, etc.
 */
$entryPoint->start();
