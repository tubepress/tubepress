<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Primary bootstrapping for TubePress.
 */
function bootTubePress()
{
    /**
     * First, record the root path.
     */
    define('TUBEPRESS_ROOT', realpath(dirname(__FILE__) . '/../../../../'));

    /**
     * Second, we add our classloader.
     */
    require_once TUBEPRESS_ROOT . '/vendor/ehough/pulsar/src/main/php/ehough/pulsar/ComposerClassLoader.php';

    $loader = new ehough_pulsar_ComposerClassLoader(TUBEPRESS_ROOT . '/vendor/');
    $loader->registerFallbackDirectory(TUBEPRESS_ROOT . '/src/main/php/classes');
    $loader->registerFallbackDirectory(TUBEPRESS_ROOT . '/src/main/php/plugins');
    $loader->register();

    /**
     * Next, set up logging.
     */
    if (isset($_GET['tubepress_debug']) && strcasecmp($_GET['tubepress_debug'], 'true') === 0) {

        $handler = new ehough_epilog_impl_handler_PrintHandler();

        $handler->setLevel(ehough_epilog_api_ILogger::DEBUG);

        ehough_epilog_api_LoggerFactory::setHandlerStack(array($handler));
    }

    /*
     * Now build the core IOC container and assign it to the kernel service locator.
     * That should be enough to get everyone else off the ground.
     */
    $coreIocContainer = new tubepress_impl_patterns_ioc_CoreIocContainer();
    tubepress_impl_patterns_ioc_KernelServiceLocator::setCoreIocContainer($coreIocContainer);

    /*
     * Finally, hand off control to the TubePress bootstrapper.
     */
    tubepress_impl_patterns_ioc_KernelServiceLocator::getBootStrapper()->boot();
}

/*
 * Don't boot twice.
 */
if (!defined('TUBEPRESS_ROOT')) {

    bootTubePress();
}
