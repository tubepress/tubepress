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
     * @var bool
     */
    private $_logEnabled = false;

    /**
     * @var tubepress_impl_boot_helper_secondary_UncachedContainerSupplier
     */
    private $_uncachedContainerSupplier;
    
    public function __construct(tubepress_api_log_LoggerInterface      $logger,
                                tubepress_impl_boot_BootSettings       $sfr)
    {
        $this->_logger             = $logger;
        $this->_settingsFileReader = $sfr;
        $this->_logEnabled         = $logger->isEnabled();
    }

    /**
     * @return tubepress_api_ioc_ContainerInterface The fully constructed service container for TubePress.
     */
    public function getServiceContainer()
    {
        if ($this->_canBootFromCache()) {

            if ($this->_logEnabled) {

                $this->_logger->debug('We can boot from the system cache. Excellent!');
            }

            try {

                return $this->_getTubePressContainerFromCache();

            } catch (RuntimeException $e) {

                //this will already have been logged
            }
        }

        return $this->_getNewTubePressContainer();
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

        if ($this->_tubePressContainerClassExists()) {

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

        $iocContainerHit = $this->_tubePressContainerClassExists();

        if ($this->_logEnabled) {

            if ($iocContainerHit) {

                $this->_logger->debug(sprintf('Service container found in cache? %s', $iocContainerHit ? 'yes' : 'no'));
            }
        }

        return $iocContainerHit;
    }

    /**
     * @return tubepress_api_ioc_ContainerInterface
     */
    private function _getTubePressContainerFromCache()
    {
        if ($this->_logEnabled) {

            $this->_logger->debug('Rehydrating cached service container.');
        }

        $this->_includeIconicContainerCache();

        /** @noinspection PhpUndefinedClassInspection */
        /**
         * @var $cachedContainer ehough_iconic_Container
         */
        $iconicContainer = new TubePressServiceContainer();

        if ($this->_logEnabled) {

            $this->_logger->debug('Done rehydrating cached service container.');
        }

        $tubePressContainer = new tubepress_impl_ioc_Container($iconicContainer);
        $this->_setEphemeralServicesToContainer($tubePressContainer, $iconicContainer);

        return $tubePressContainer;
    }

    private function _includeIconicContainerCache()
    {
        if ($this->_tubePressContainerClassExists()) {

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

    private function _tubePressContainerClassExists()
    {
        return class_exists('TubePressServiceContainer', false);
    }

    /**
     * @return tubepress_api_ioc_ContainerInterface
     */
    private function _getNewTubePressContainer()
    {
        if ($this->_logEnabled) {

            $this->_logger->debug('We cannot boot from cache. Will perform a full boot instead.');
        }

        if (!class_exists('ehough_pulsar_MapClassLoader', false)) {

            /** @noinspection PhpIncludeInspection */
            require TUBEPRESS_ROOT . '/vendor/ehough/pulsar/src/main/php/ehough/pulsar/MapClassLoader.php';
        }

        /**
         * Create a temporary classloader so we can do the full boot.
         */
        /** @noinspection PhpIncludeInspection */
        $fullClassMap = require TUBEPRESS_ROOT . '/src/platform/scripts/classloading/classmap.php';
        $classLoader  = new ehough_pulsar_MapClassLoader($fullClassMap);
        $classLoader->register();

        if (!isset($this->_uncachedContainerSupplier)) {

            $finderFactory = new ehough_finder_FinderFactory();
            $uncached      = new tubepress_impl_boot_helper_secondary_UncachedContainerSupplier(

                $this->_logger,
                new tubepress_impl_boot_helper_secondary_ClassLoaderPrimer($this->_logger),
                new tubepress_impl_addon_Registry($this->_logger, $this->_settingsFileReader, $finderFactory),
                new tubepress_impl_boot_helper_secondary_IocCompiler($this->_logger),
                $this->_settingsFileReader
            );

            $this->_uncachedContainerSupplier = $uncached;
        }

        $result             = $this->_uncachedContainerSupplier->getNewIconicContainer($this->_settingsFileReader);
        $tubePressContainer = new tubepress_impl_ioc_Container($result);

        /**
         * De-register the temporary classloader.
         */
        spl_autoload_unregister(array($classLoader, 'loadClass'));

        if ($result instanceof ehough_iconic_ContainerBuilder) {

            return $tubePressContainer;
        }

        $this->_setEphemeralServicesToContainer($tubePressContainer, $result);

        return $tubePressContainer;
    }

    private function _setEphemeralServicesToContainer(tubepress_api_ioc_ContainerInterface $tubePressContainer,
                                                      ehough_iconic_ContainerInterface     $iconicContainer)
    {
        $tubePressContainer->set('tubepress_api_ioc_ContainerInterface',      $tubePressContainer);
        $tubePressContainer->set('ehough_iconic_ContainerInterface',          $iconicContainer);
        $tubePressContainer->set('tubepress_impl_log_BootLogger',             $this->_logger);
        $tubePressContainer->set(tubepress_api_boot_BootSettingsInterface::_, $this->_settingsFileReader);
    }

    public function ___setUncachedContainerSupplier(tubepress_impl_boot_helper_secondary_UncachedContainerSupplier $supplier)
    {
        $this->_uncachedContainerSupplier = $supplier;
    }
}
