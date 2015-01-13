<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
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
class tubepress_platform_impl_boot_helper_ContainerSupplier
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_platform_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var bool
     */
    private $_logEnabled = false;

    /**
     * @var tubepress_platform_impl_boot_helper_uncached_UncachedContainerSupplier
     */
    private $_uncachedContainerSupplier;

    /**
     * @var ehough_pulsar_MapClassLoader
     */
    private $_temporaryClassLoader;

    public function __construct(tubepress_platform_api_log_LoggerInterface        $logger,
                                tubepress_platform_api_boot_BootSettingsInterface $bootSettings)
    {
        $this->_logger       = $logger;
        $this->_bootSettings = $bootSettings;
        $this->_logEnabled   = $logger->isEnabled();
    }

    /**
     * @return tubepress_platform_api_ioc_ContainerInterface The fully constructed service container for TubePress.
     */
    public function getServiceContainer()
    {
        if ($this->_canBootFromCache()) {

            if ($this->_logEnabled) {

                $this->_logger->debug('System cache is available. Excellent!');
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

            $this->_logger->debug('Determining if system cache is available.');
        }

        if (!$this->_bootSettings->isSystemCacheEnabled()) {

            if ($this->_logEnabled) {

                $this->_logger->debug('System cache is disabled by user settings.php');
            }

            return false;
        }

        if ($this->_tubePressContainerClassExists()) {

            return true;
        }

        $file = $this->_getPathToContainerCacheFile();

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
     * @return tubepress_platform_api_ioc_ContainerInterface
     */
    private function _getTubePressContainerFromCache()
    {
        if ($this->_logEnabled) {

            $this->_logger->debug('Rehydrating cached service container.');
        }

        /** @noinspection PhpUndefinedClassInspection */
        /**
         * @var $cachedContainer ehough_iconic_Container
         */
        $iconicContainer = new TubePressServiceContainer();

        if ($this->_logEnabled) {

            $this->_logger->debug('Done rehydrating cached service container.');
        }

        $tubePressContainer = new tubepress_platform_impl_ioc_Container($iconicContainer);

        $this->_setEphemeralServicesToContainer($tubePressContainer, $iconicContainer);

        return $tubePressContainer;
    }

    private function _tubePressContainerClassExists()
    {
        return class_exists('TubePressServiceContainer', false);
    }

    /**
     * @return tubepress_platform_api_ioc_ContainerInterface
     */
    private function _getNewTubePressContainer()
    {
        if ($this->_logEnabled) {

            $this->_logger->debug('We cannot boot from cache. Will perform a full boot instead.');
        }

        $this->_buildTemporaryClassLoader();
        $this->_buildUncachedContainerSupplier();

        $result             = $this->_uncachedContainerSupplier->getNewIconicContainer($this->_bootSettings);
        $tubePressContainer = new tubepress_platform_impl_ioc_Container($result);

        spl_autoload_unregister(array($this->_temporaryClassLoader, 'loadClass'));

        $this->_setEphemeralServicesToContainer($tubePressContainer, $result);

        return $tubePressContainer;
    }

    private function _setEphemeralServicesToContainer(tubepress_platform_api_ioc_ContainerInterface $tubePressContainer,
                                                      ehough_iconic_ContainerInterface              $iconicContainer)
    {
        $tubePressContainer->set('tubepress_platform_api_ioc_ContainerInterface',      $tubePressContainer);
        $tubePressContainer->set('ehough_iconic_ContainerInterface',                   $iconicContainer);
        $tubePressContainer->set('tubepress_platform_impl_log_BootLogger',             $this->_logger);
        $tubePressContainer->set(tubepress_platform_api_boot_BootSettingsInterface::_, $this->_bootSettings);
    }

    private function _getPathToContainerCacheFile()
    {
        $cachePath = $this->_bootSettings->getPathToSystemCacheDirectory();

        return sprintf('%s%sTubePress-%s-ServiceContainer.php', $cachePath, DIRECTORY_SEPARATOR, TUBEPRESS_VERSION);
    }

    private function _buildTemporaryClassLoader()
    {
        if (!class_exists('ehough_pulsar_MapClassLoader', false)) {

            require TUBEPRESS_ROOT . '/vendor/ehough/pulsar/src/main/php/ehough/pulsar/MapClassLoader.php';
        }

        /**
         * Create a temporary classloader so we can do the full boot.
         */
        /** @noinspection PhpIncludeInspection */
        $fullClassMap = require TUBEPRESS_ROOT . '/src/platform/scripts/classloading/classmap.php';
        $this->_temporaryClassLoader  = new ehough_pulsar_MapClassLoader($fullClassMap);
        $this->_temporaryClassLoader->register();
    }

    private function _buildUncachedContainerSupplier()
    {
        if (isset($this->_uncachedContainerSupplier)) {

            return;
        }

        $finderFactory = new ehough_finder_FinderFactory();
        $urlFactory    = new tubepress_platform_impl_url_puzzle_UrlFactory();
        $langUtils     = new tubepress_platform_impl_util_LangUtils();
        $stringUtils   = new tubepress_platform_impl_util_StringUtils();
        $addonFactory  = new tubepress_platform_impl_boot_helper_uncached_contrib_AddonFactory(
            $this->_logger, $urlFactory, $langUtils, $stringUtils, $this->_bootSettings
        );
        $manifestFinder = new tubepress_platform_impl_boot_helper_uncached_contrib_ManifestFinder(
            TUBEPRESS_ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'add-ons', DIRECTORY_SEPARATOR . 'add-ons', 'manifest.json',
            $this->_logger, $this->_bootSettings, $finderFactory
        );
        $uncached = new tubepress_platform_impl_boot_helper_uncached_UncachedContainerSupplier(

            $this->_logger, $manifestFinder, $addonFactory,
            new tubepress_platform_impl_boot_helper_uncached_Compiler($this->_logger),
            $this->_bootSettings
        );

        $this->_uncachedContainerSupplier = $uncached;
    }

    public function ___setUncachedContainerSupplier(tubepress_platform_impl_boot_helper_uncached_UncachedContainerSupplier $supplier)
    {
        $this->_uncachedContainerSupplier = $supplier;
    }
}
