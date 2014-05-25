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
 *
 */
class tubepress_impl_boot_helper_ContainerSupplier
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_impl_boot_BootSettings
     */
    private $_settingsFileReader;

    /**
     * @var ehough_pulsar_ComposerClassLoader
     */
    private $_classLoader;

    /**
     * @var bool
     */
    private $_logEnabled = false;

    /**
     * @var tubepress_impl_boot_helper_secondary_UncachedContainerSupplier
     */
    private $_uncachedContainerSupplier;

    /**
     * @var tubepress_impl_boot_helper_secondary_CachedContainerSupplier
     */
    private $_cachedContainerSupplier;
    
    public function __construct(

        tubepress_api_log_LoggerInterface $logger,
        tubepress_impl_boot_BootSettings $sfr,
        ehough_pulsar_ComposerClassLoader $classLoader)
    {
        $this->_logger             = $logger;
        $this->_settingsFileReader = $sfr;
        $this->_logEnabled         = $logger->isEnabled();
        $this->_classLoader        = $classLoader;
    }

    /**
     * @return tubepress_api_ioc_ContainerInterface The fully constructed service container for TubePress.
     */
    public function getServiceContainer()
    {
        $iconicContainer = $this->_getIconicContainer();

        return new tubepress_impl_ioc_IconicContainer($iconicContainer);
    }

    private function _getIconicContainer()
    {
        if ($this->_canBootFromCache()) {

            if ($this->_logEnabled) {

                $this->_logger->debug('We can boot from cache. Excellent!');
            }

            if (!isset($this->_cachedContainerSupplier)) {

                $this->_cachedContainerSupplier = new tubepress_impl_boot_helper_secondary_CachedContainerSupplier($this->_logger, $this->_settingsFileReader);
            }

            try {

                return $this->_cachedContainerSupplier->getServiceContainer($this->_settingsFileReader);

            } catch (RuntimeException $e) {

                //this will already have been logged
            }
        }

        if ($this->_logEnabled) {

            $this->_logger->debug('We cannot boot from cache. Will perform a full boot instead.');
        }

        if (!isset($this->_uncachedContainerSupplier)) {

            $finderFactory = new ehough_finder_FinderFactory();
            $uncached      = new tubepress_impl_boot_helper_secondary_UncachedContainerSupplier(

                $this->_logger,
                new tubepress_impl_boot_helper_secondary_ClassLoaderPrimer($this->_logger),
                new tubepress_impl_addon_Registry($this->_logger, $this->_settingsFileReader, $finderFactory),
                new tubepress_impl_boot_helper_secondary_IocCompiler($this->_logger),
                $this->_classLoader,
                $this->_settingsFileReader
            );

            $this->_uncachedContainerSupplier = $uncached;
        }

        return $this->_uncachedContainerSupplier->getServiceContainer($this->_settingsFileReader, $this->_classLoader);
    }

    /**
     * @return bool True if we are able to boot from a cached container. False otherwise.
     */
    private function _canBootFromCache()
    {
        if ($this->_logEnabled) {

            $this->_logger->debug('Determining if we can boot from the cache.');
        }

        if (!$this->_settingsFileReader->isContainerCacheEnabled()) {

            if ($this->_logEnabled) {

                $this->_logger->debug('Boot cache is disabled by user settings.php');
            }

            return false;
        }

        if (class_exists('TubePressServiceContainer', false)) {

            return true;
        }

        $file = $this->_settingsFileReader->getPathToContainerCacheFile();;

        if (!is_readable($file)) {

            if ($this->_logEnabled) {

                $this->_logger->debug(sprintf('%s is not a readable file.', $file));
            }

            return false;
        }

        if ($this->_logEnabled) {

            $this->_logger->debug(sprintf('%s is a readable file. Now including it.', $file));
        }

        /** @noinspection PhpIncludeInspection */
        require $file;

        $iocContainerHit = class_exists('TubePressServiceContainer', false);

        if ($this->_logEnabled) {

            if ($iocContainerHit) {

                $this->_logger->debug(sprintf('IOC container found in cache? %s', $iocContainerHit ? 'yes' : 'no'));
            }
        }

        return $iocContainerHit;
    }

    public function ___setCachedContainerSupplier(tubepress_impl_boot_helper_secondary_CachedContainerSupplier $supplier)
    {
        $this->_cachedContainerSupplier = $supplier;
    }

    public function ___setUncachedContainerSupplier(tubepress_impl_boot_helper_secondary_UncachedContainerSupplier $supplier)
    {
        $this->_uncachedContainerSupplier = $supplier;
    }
}
