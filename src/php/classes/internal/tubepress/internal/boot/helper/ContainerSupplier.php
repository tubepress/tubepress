<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
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
class tubepress_internal_boot_helper_ContainerSupplier
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var bool
     */
    private $_logEnabled = false;

    /**
     * @var tubepress_internal_boot_helper_uncached_UncachedContainerSupplier
     */
    private $_uncachedContainerSupplier;

    /**
     * @var Symfony\Component\ClassLoader\MapClassLoader
     */
    private $_temporaryClassLoader;

    public function __construct(tubepress_api_log_LoggerInterface        $logger,
                                tubepress_api_boot_BootSettingsInterface $bootSettings)
    {
        $this->_logger       = $logger;
        $this->_bootSettings = $bootSettings;
        $this->_logEnabled   = $logger->isEnabled();
    }

    /**
     * @return tubepress_api_ioc_ContainerInterface The fully constructed service container for TubePress.
     */
    public function getServiceContainer()
    {
        if ($this->_canBootFromCache()) {

            if ($this->_logEnabled) {

                $this->_logDebug('System cache is available. Excellent!');
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

            $this->_logDebug('Determining if system cache is available.');
        }

        if (!$this->_bootSettings->isSystemCacheEnabled()) {

            if ($this->_logEnabled) {

                $this->_logDebug('System cache is disabled by user settings.php');
            }

            return false;
        }

        if ($this->_tubePressContainerClassExists()) {

            return true;
        }

        $file = $this->_getPathToContainerCacheFile();

        if (!is_readable($file)) {

            if ($this->_logEnabled) {

                $this->_logDebug(sprintf('<code>%s</code> is not a readable file.', $file));
            }

            return false;
        }

        if ($this->_logEnabled) {

            $this->_logDebug(sprintf('<code>%s</code> is a readable file. Now including it.', $file));
        }

        /** @noinspection PhpIncludeInspection */
        require $file;

        $iocContainerHit = $this->_tubePressContainerClassExists();

        if ($this->_logEnabled) {

            if ($iocContainerHit) {

                $this->_logDebug(sprintf('Service container found in cache? <code>%s</code>', $iocContainerHit ? 'yes' : 'no'));
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

            $this->_logDebug('Rehydrating cached service container.');
        }

        /** @noinspection PhpUndefinedClassInspection */
        /**
         * @var $cachedContainer \Symfony\Component\DependencyInjection\ContainerInterface
         */
        $symfonyContainer = new TubePressServiceContainer();

        if ($this->_logEnabled) {

            $this->_logDebug('Done rehydrating cached service container.');
        }

        $tubePressContainer = new tubepress_internal_ioc_Container($symfonyContainer);

        $this->_setEphemeralServicesToContainer($tubePressContainer, $symfonyContainer);

        return $tubePressContainer;
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

            $this->_logDebug('We cannot boot from cache. Will perform a full boot instead.');
        }

        $this->_buildTemporaryClassLoader();
        $this->_buildUncachedContainerSupplier();

        $result             = $this->_uncachedContainerSupplier->getNewSymfonyContainer();
        $tubePressContainer = new tubepress_internal_ioc_Container($result);

        spl_autoload_unregister(array($this->_temporaryClassLoader, 'loadClass'));

        $this->_setEphemeralServicesToContainer($tubePressContainer, $result);

        return $tubePressContainer;
    }

    private function _setEphemeralServicesToContainer(tubepress_api_ioc_ContainerInterface                      $tubePressContainer,
                                                      \Symfony\Component\DependencyInjection\ContainerInterface $symfonyContainer)
    {
        $tubePressContainer->set('tubepress_api_ioc_ContainerInterface',      $tubePressContainer);
        $tubePressContainer->set('symfony_service_container',                 $symfonyContainer);
        $tubePressContainer->set('tubepress_internal_logger_BootLogger',      $this->_logger);
        $tubePressContainer->set(tubepress_api_boot_BootSettingsInterface::_, $this->_bootSettings);
    }

    private function _getPathToContainerCacheFile()
    {
        $cachePath = $this->_bootSettings->getPathToSystemCacheDirectory();

        return sprintf('%s%sTubePressServiceContainer.php', $cachePath, DIRECTORY_SEPARATOR);
    }

    private function _buildTemporaryClassLoader()
    {
        if (!class_exists('Symfony\Component\ClassLoader\MapClassLoader', false)) {

            require TUBEPRESS_ROOT . '/vendor/symfony/class-loader/MapClassLoader.php';
        }

        /**
         * Create a temporary classloader so we can do the full boot.
         */
        /** @noinspection PhpIncludeInspection */
        $fullClassMap = require TUBEPRESS_ROOT . '/src/php/scripts/classloading/classmap.php';
        $this->_temporaryClassLoader  = new \Symfony\Component\ClassLoader\MapClassLoader($fullClassMap);
        $this->_temporaryClassLoader->register();
    }

    private function _buildUncachedContainerSupplier()
    {
        if (isset($this->_uncachedContainerSupplier)) {

            return;
        }

        $finderFactory = new tubepress_internal_finder_FinderFactory();
        $urlFactory    = new tubepress_url_impl_puzzle_UrlFactory();
        $langUtils     = new tubepress_util_impl_LangUtils();
        $stringUtils   = new tubepress_util_impl_StringUtils();
        $addonFactory  = new tubepress_internal_boot_helper_uncached_contrib_AddonFactory(
            $this->_logger, $urlFactory, $langUtils, $stringUtils, $this->_bootSettings
        );
        $manifestFinder = new tubepress_internal_boot_helper_uncached_contrib_ManifestFinder(
            TUBEPRESS_ROOT . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'add-ons', 
            DIRECTORY_SEPARATOR . 'add-ons',
            'manifest.json',
            $this->_logger,
            $this->_bootSettings,
            $finderFactory
        );
        $uncached = new tubepress_internal_boot_helper_uncached_UncachedContainerSupplier(

            $this->_logger, $manifestFinder, $addonFactory,
            new tubepress_internal_boot_helper_uncached_Compiler($this->_logger),
            $this->_bootSettings
        );

        $this->_uncachedContainerSupplier = $uncached;
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Container Supplier) %s', $msg));
    }

    public function ___setUncachedContainerSupplier(tubepress_internal_boot_helper_uncached_UncachedContainerSupplier $supplier)
    {
        $this->_uncachedContainerSupplier = $supplier;
    }
}
