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
 * Performs TubePress-wide initialization. This is a complicated
 * and somewhat delicate process. Take your time and read carefully!
 */
class tubepress_impl_boot_PrimaryBootstrapper
{
    /**
     * @var tubepress_api_ioc_ContainerInterface
     */
    private static $_SERVICE_CONTAINER;

    /**
     * @var Exception
     */
    private static $_BOOT_EXCEPTION;

    /**
     * @var tubepress_impl_log_BootLogger
     */
    private $_bootLogger;

    /**
     * @var float
     */
    private $_startTime;

    /**
     * @var tubepress_api_boot_BootSettingsInterface
     */
    private $_helperSettingsFileReader;

    /**
     * @var tubepress_impl_boot_helper_ContainerSupplier
     */
    private $_helperContainerSupplier;

    /**
     * Performs TubePress-wide initialization.
     *
     * @throws Exception If an error was encountered during boot.
     *
     * @return tubepress_api_ioc_ContainerInterface
     */
    public function getServiceContainer()
    {
        /**
         * Don't boot twice!
         */
        if (isset(self::$_SERVICE_CONTAINER)) {

            return self::$_SERVICE_CONTAINER;
        }

        if (isset(self::$_BOOT_EXCEPTION)) {

            throw self::$_BOOT_EXCEPTION;
        }

        try {

            $this->_wrappedBoot();

        } catch (Exception $e) {

            $this->_handleBootException($e);
        }

        return self::$_SERVICE_CONTAINER;
    }

    /**
     * @return void
     */
    private function _wrappedBoot()
    {
        /**
         * Setup initial class loader.
         */
        $this->_01_registerMinimalClassLoader();

        /*
         * Setup basic logging facilities.
         */
        $this->_02_buildBootLogger();

        /**
         * Record start time.
         */
        $this->_03_recordStartTime();

        /**
         * Helper services.
         */
        $this->_04_buildHelperServices();

        /**
         * Core boot.
         */
        $this->_05_loadServiceContainer();

        /**
         * Setup classloading.
         */
        $this->_06_registerClassLoaderIfRequested();
        
        /**
         * Record finish time.
         */
        $this->_07_recordFinishTime();

        /**
         * Free up some memory.
         */
        $this->_08_freeMemory();
    }

    private function _01_registerMinimalClassLoader()
    {
        /**
         * We don't want to include this during unit tests, so simply check to see if a mock boot logger
         * has already been set on the object.
         */
        if (!isset($this->_bootLogger)) {

            /** @noinspection PhpIncludeInspection */
            require TUBEPRESS_ROOT . '/src/platform/scripts/class-collections/minimal-boot.php';
        }
    }

    private function _02_buildBootLogger()
    {
        if (!isset($this->_bootLogger)) {

            $loggingRequested  = isset($_GET['tubepress_debug']) && strcasecmp($_GET['tubepress_debug'], 'true') === 0;
            $this->_bootLogger = new tubepress_impl_log_BootLogger($loggingRequested);
        }
    }

    private function _03_recordStartTime()
    {
        if ($this->_bootLogger->isEnabled()) {

            /**
             * Keep track of how long this takes.
             */
            $this->_startTime = microtime(true);
        }
    }
    
    private function _04_buildHelperServices()
    {
        if (!isset($this->_helperSettingsFileReader)) {

            $this->_helperSettingsFileReader = new tubepress_impl_boot_BootSettings($this->_bootLogger);
        }

        if (!isset($this->_helperContainerSupplier)) {

            $this->_helperContainerSupplier = new tubepress_impl_boot_helper_ContainerSupplier(
                
                $this->_bootLogger,
                $this->_helperSettingsFileReader
            );
        }
    }

    private function _05_loadServiceContainer()
    {
        self::$_SERVICE_CONTAINER = $this->_helperContainerSupplier->getServiceContainer();
    }

    private function _06_registerClassLoaderIfRequested()
    {
        $container = self::$_SERVICE_CONTAINER;
        
        if (!$this->_helperSettingsFileReader->isClassLoaderEnabled()) {

            return;
        }

        if ($container->hasParameter('classloading-classmap')) {

            $containerClassMap = $container->getParameter('classloading-classmap');
            $mapClassLoader    = new ehough_pulsar_MapClassLoader($containerClassMap);

            $mapClassLoader->register();
        }

        $hasFallbacks = $container->hasParameter('classloading-psr0-fallbacks');
        $hasPrefixed  = $container->hasParameter('classloading-psr0-prefixed-paths');

        if (!$hasFallbacks && !$hasPrefixed) {

            return;
        }

        $psr0Fallbacks     = $container->getParameter('classloading-psr0-fallbacks');
        $psr0PrefixedPaths = $container->getParameter('classloading-psr0-prefixed-paths');

        if (empty($psr0Fallbacks) && empty($psr0PrefixedPaths)) {
            
            return;
        }
        
        $universalClassLoader = new ehough_pulsar_UniversalClassLoader();
        
        $universalClassLoader->registerNamespaces($psr0PrefixedPaths);
        $universalClassLoader->registerPrefixes($psr0PrefixedPaths);
        $universalClassLoader->registerNamespaceFallbacks($psr0Fallbacks);
        $universalClassLoader->registerPrefixFallbacks($psr0Fallbacks);
        $universalClassLoader->register();
    }

    private function _07_recordFinishTime()
    {
        if (!$this->_bootLogger->isEnabled()) {

            return;
        }

        $now = microtime(true);

        $this->_bootLogger->debug(sprintf('Boot completed in %f milliseconds',
            (($now - $this->_startTime) * 1000.0)));

        /**
         * @var $realLogger tubepress_api_log_LoggerInterface
         */
        $realLogger = self::$_SERVICE_CONTAINER->get(tubepress_api_log_LoggerInterface::_);

        /**
         * Flush the boot logger to the real logger.
         */
        $this->_bootLogger->flushTo($realLogger);
        $this->_bootLogger->onBootComplete();

        /**
         * Activate the real logger.
         */
        $realLogger->onBootComplete();
    }

    private function _08_freeMemory()
    {
        unset($this->_helperSettingsFileReader);
        unset($this->_helperContainerSupplier);
    }

    private function _handleBootException(Exception $e)
    {
        if ($this->_bootLogger->isEnabled()) {

            /**
             * Print everything out, including a stack trace.
             */
            $this->_bootLogger->handleBootException($e);
            $this->_bootLogger->onBootComplete();
        }

        self::$_BOOT_EXCEPTION = $e;

        throw $e;
    }


    /***********************************************************************************************************
     *** TESTING FUNCTIONS *************************************************************************************
     **********************************************************************************************************/

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_api_boot_BootSettingsInterface $bcsi The settings file interface.
     *
     * @internal
     */
    public function ___setSettingsFileReader(tubepress_api_boot_BootSettingsInterface $bcsi)
    {
        $this->_helperSettingsFileReader = $bcsi;
    }

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_impl_boot_helper_ContainerSupplier $sbi The container supplier.
     *
     * @internal
     */
    public function ___setContainerSupplier(tubepress_impl_boot_helper_ContainerSupplier $sbi)
    {
        $this->_helperContainerSupplier = $sbi;
    }

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_impl_log_BootLogger $logger
     *
     * @internal
     */
    public function ___setTemporaryLogger(tubepress_impl_log_BootLogger $logger)
    {
        $this->_bootLogger = $logger;
    }
}