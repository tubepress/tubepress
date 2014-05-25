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

/**
 * Retrieves settings from a PHP file.
 */
class tubepress_impl_boot_helper_secondary_CachedContainerSupplier
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var tubepress_impl_boot_BootSettings
     */
    private $_settingsFileReader;

    public function __construct(tubepress_api_log_LoggerInterface $logger, tubepress_impl_boot_BootSettings $srfi)
    {
        $this->_logger             = $logger;
        $this->_shouldLog          = $logger->isEnabled();
        $this->_settingsFileReader = $srfi;
    }

    /**
     * @return ehough_iconic_ContainerInterface
     */
    public function getServiceContainer()
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Retrieving and preparing cached IOC container.');
        }

        $this->_readCachedContainerIfNecessary();

        /** @noinspection PhpUndefinedClassInspection */
        /**
         * @var $cachedContainer ehough_iconic_Container
         */
        $container = new TubePressServiceContainer();

        if ($this->_shouldLog) {

            $this->_logger->debug('Done restoring cached IOC container.');
        }

        return $container;
    }

    private function _readCachedContainerIfNecessary()
    {
        if (class_exists('TubePressServiceContainer', false)) {

            return;
        }

        $file = $this->_settingsFileReader->getPathToContainerCacheFile();;

        if (!is_readable($file)) {

            $message = sprintf('Cannot read file at %s', $file);

            $this->_logger->error($message);

            throw new RuntimeException($message);
        }

        /** @noinspection PhpIncludeInspection */
        require $file;
    }
}
