<?php
/**
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_internal_boot_InitialBootstrapper
{
    /**
     * @var tubepress_api_ioc_ContainerInterface
     */
    public static $SERVICE_CONTAINER;

    /**
     * @var tubepress_internal_boot_PrimaryBootstrapper
     */
    private static $_PRIMARY_BOOTSTRAPPER;

    /**
     * Primary bootstrapping for TubePress.
     *
     * @return tubepress_api_ioc_ContainerInterface
     */
    public static function getServiceContainer()
    {
        if (!isset(self::$SERVICE_CONTAINER)) {

            self::_buildServiceContainer();
        }

        return self::$SERVICE_CONTAINER;
    }

    /**
     * THIS SHOULD NOT BE CALLED OUTSIDE OF TESTING.
     *
     * @param tubepress_internal_boot_PrimaryBootstrapper $bootstrapper
     */
    public static function __setPrimaryBootstrapper(tubepress_internal_boot_PrimaryBootstrapper $bootstrapper)
    {
        self::$_PRIMARY_BOOTSTRAPPER = $bootstrapper;
    }

    private static function _buildServiceContainer()
    {
        /**
         * First, record the root path.
         */
        if (!defined('TUBEPRESS_ROOT')) {

            define('TUBEPRESS_ROOT', self::_calculateTubePressRoot());
        }

        if (!defined('TUBEPRESS_VERSION')) {

            /**
             * This is set to the actual version during packaging.
             */
            define('TUBEPRESS_VERSION', '99.99.99');
        }

        if (!isset(self::$_PRIMARY_BOOTSTRAPPER)) {

            /**
             * Finally, load the primary bootstrapper and use it to obtain the service container.
             */
            if (!class_exists('tubepress_internal_boot_PrimaryBootstrapper', false)) {

                require TUBEPRESS_ROOT . '/src/php/classes/internal/tubepress/internal/boot/PrimaryBootstrapper.php';
            }

            self::$_PRIMARY_BOOTSTRAPPER = new tubepress_internal_boot_PrimaryBootstrapper();
        }

        self::$SERVICE_CONTAINER = self::$_PRIMARY_BOOTSTRAPPER->getServiceContainer();
    }

    private static function _calculateTubePressRoot()
    {
        /**
         * We could call realpath() here but it's too darn expensive. So instead we peel off
         * the last few dirs.
         */
        $thisPath = __FILE__;

        /*
         * $thisPath will be something like /home/bla/code/tubepress/src/php/classes/internal/tubepress/internal/boot/InitialBootstrapper.php
         */
        $toRemove = array('src', 'php', 'classes', 'internal', 'tubepress', 'internal', 'boot', 'InitialBootstrapper.php');
        $toRemove = DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $toRemove);
        $toReturn = str_replace($toRemove, '', $thisPath);

        return $toReturn;
    }
}