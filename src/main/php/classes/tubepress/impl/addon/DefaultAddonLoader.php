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

/**
 * Simple add-on loader.
 */
class tubepress_impl_addon_DefaultAddonLoader implements tubepress_spi_addon_AddonLoader
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Default Add-on Loader');
    }

    /**
     * Loads the given add-on into the system.
     *
     * @param tubepress_spi_addon_Addon $addon
     *
     * @return mixed Null if the add-on loaded normally, otherwise a string error message.
     */
    public final function load(tubepress_spi_addon_Addon $addon)
    {
        $bootstrap          = $addon->getBootstrap();
        $isDebuggingEnabled = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        if (!$bootstrap) {

            if ($isDebuggingEnabled) {

                $this->_logger->debug(sprintf('%s did not define a bootstrap', $addon->getName()));
            }

            return null;
        }

        if (is_file($bootstrap) && is_readable($bootstrap)) {

            if ($isDebuggingEnabled) {

                $this->_logger->debug(sprintf('%s add-on\'s bootstrap (%s) is a readable file.',
                    $addon->getName(), $bootstrap));
            }

            $callback = array($this, '_callbackIncludeFile');

        } else {

            if ($isDebuggingEnabled) {

                $this->_logger->debug(sprintf('%s add-on\'s bootstrap (%s) is not a readable file. Assuming it\'s a class name or service ID...',
                    $addon->getName(), $bootstrap));
            }

            $callback = array($this, '_callbackCallBootFunction');
        }

        try {

            call_user_func($callback, $bootstrap);

        } catch (Exception $e) {

            return 'Hit exception when trying to load ' . $addon->getName() . ': ' . $e->getMessage();
        }

        return null;
    }

    public function _callbackCallBootFunction($bootstrap)
    {
        /**
         * First let's see if the service locator knows about it.
         */
        $instance = null;

        try {

            $instance = tubepress_impl_patterns_sl_ServiceLocator::getService($bootstrap);

        } catch (Exception $e) {

            //ignore for now
        }

        if ($instance === null) {

            $ref      = new ReflectionClass($bootstrap);
            $instance = $ref->newInstance();
        }

        $instance->boot();
    }

    public function _callbackIncludeFile($bootstrap)
    {
        if ($this->_logger->isHandling(ehough_epilog_Logger::DEBUG)) {

            $this->_logger->debug(sprintf('Now including bootstrap (%s)', $bootstrap));
        }

        /** @noinspection PhpIncludeInspection */
        include $bootstrap;
    }
}
