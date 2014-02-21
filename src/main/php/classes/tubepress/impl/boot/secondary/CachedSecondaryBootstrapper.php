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
class tubepress_impl_boot_secondary_CachedSecondaryBootstrapper implements tubepress_spi_boot_secondary_SecondaryBootstrapperInterface
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(
        $shouldLog)
    {
        $this->_logger    = ehough_epilog_LoggerFactory::getLogger('Cached Secondary Bootstrapper');
        $this->_shouldLog = $shouldLog;
    }

    public function getServiceContainer(
        tubepress_spi_boot_SettingsFileReaderInterface $sfri,
        ehough_pulsar_ComposerClassLoader $classLoader
    )
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Retrieving and preparing cached IOC container.');
        }

        $this->_readCachedContainerIfNecessary($sfri);

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

    private function _readCachedContainerIfNecessary(tubepress_spi_boot_SettingsFileReaderInterface $sfri)
    {
        if (class_exists('TubePressServiceContainer', false)) {

            return;
        }

        $file = $sfri->getCachedContainerStoragePath();;

        if (!is_readable($file)) {

            return;
        }

        /** @noinspection PhpIncludeInspection */
        require $file;
    }
}
