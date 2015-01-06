<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

if (!class_exists('__tubePressBoot', false)) {

    class __tubePressBoot
    {
        public static $SERVICE_CONTAINER = null;

        /**
         * Primary bootstrapping for TubePress.
         */
        public static function getServiceContainer()
        {
            if (!isset(self::$SERVICE_CONTAINER)) {

                self::_cacheServiceContainer();
            }

            return self::$SERVICE_CONTAINER;
        }

        private static function _cacheServiceContainer()
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

            if (!class_exists('tubepress_platform_impl_boot_PrimaryBootstrapper', false)) {

                require TUBEPRESS_ROOT . '/src/platform/classes/tubepress/platform/impl/boot/PrimaryBootstrapper.php';
            }

            $bootStrapper = new tubepress_platform_impl_boot_PrimaryBootstrapper();
            self::$SERVICE_CONTAINER = $bootStrapper->getServiceContainer();
        }

        private static function _calculateTubePressRoot()
        {
            /**
             * We could call realpath() here but it's too darn expensive. So instead we peel off
             * the last three dirs.
             */
            $thisPath = __FILE__;

            /*
             * Start with /home/bla/code/tubepress/src/platform/scripts/boot.php
             */
            $toRemove = DIRECTORY_SEPARATOR . 'src' .
                        DIRECTORY_SEPARATOR . 'platform' .
                        DIRECTORY_SEPARATOR . 'scripts' .
                        DIRECTORY_SEPARATOR . 'boot.php';

            return str_replace($toRemove, '', $thisPath);
        }
    }
}

return __tubePressBoot::getServiceContainer();