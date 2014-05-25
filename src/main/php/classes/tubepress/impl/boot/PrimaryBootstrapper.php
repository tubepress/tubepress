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
     * @var tubepress_impl_log_BootLogger
     */
    private $_logger;

    /**
     * @var float
     */
    private $_startTime;

    /**
     * @var ehough_pulsar_ComposerClassLoader
     */
    private $_classLoader;

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

        try {

            $this->_wrappedBoot();

        } catch (Exception $e) {

            if ($this->_logger->isEnabled()) {

                $this->_logger->error('Caught exception while booting: '.  $e->getMessage());

                //flush out log statements
                $this->_logger->printBuffer();
            }

            throw $e;
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
        $this->_01_buildInitialClassLoader();

        /*
         * Setup basic logging facilities.
         */
        $this->_02_buildTemporaryLogger();

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
         * Record finish time.
         */
        $this->_06_recordFinishTime();

        /**
         * Flush the log if we need to.
         */
        $this->_07_finishLoggingJobs();

        /**
         * Free up some memory.
         */
        $this->_08_freeMemory();
    }

    private function _01_buildInitialClassLoader()
    {
        if (!isset($this->_classLoader)) {

            if (! class_exists('ehough_pulsar_ComposerClassLoader', false)) {

                require_once TUBEPRESS_ROOT . '/vendor/ehough/pulsar/src/main/php/ehough/pulsar/ComposerClassLoader.php';
            }

            $this->_classLoader = new ehough_pulsar_ComposerClassLoader(TUBEPRESS_ROOT . '/vendor/');
        }

        $this->_classLoader->register();

        $bootStrapClassMap = require_once TUBEPRESS_ROOT . '/src/main/php/scripts/classmaps/bootstrap.php';

        $this->_classLoader->addToClassMap($bootStrapClassMap);
    }

    private function _02_buildTemporaryLogger()
    {
        if (!isset($this->_logger)) {

            $loggingRequested = isset($_GET['tubepress_debug']) && strcasecmp($_GET['tubepress_debug'], 'true') === 0;
            $this->_logger    = new tubepress_impl_log_BootLogger($loggingRequested);
        }
    }

    private function _03_recordStartTime()
    {
        if ($this->_logger->isEnabled()) {

            /**
             * Keep track of how long this takes.
             */
            $this->_startTime = microtime(true);
        }
    }
    
    private function _04_buildHelperServices()
    {
        if (!isset($this->_helperSettingsFileReader)) {

            $this->_helperSettingsFileReader = new tubepress_impl_boot_BootSettings($this->_logger);
        }

        if (!isset($this->_helperContainerSupplier)) {

            $this->_helperContainerSupplier = new tubepress_impl_boot_helper_ContainerSupplier(
                
                $this->_logger,
                $this->_helperSettingsFileReader,
                $this->_classLoader
            );
        }
    }

    private function _05_loadServiceContainer()
    {
        $container = $this->_helperContainerSupplier->getServiceContainer();

        if ($this->_helperSettingsFileReader->isClassLoaderEnabled()) {

            $containerClassMap = $container->getParameter('classMap');
            
            $this->_classLoader->addToClassMap($containerClassMap);
        }

        /**
         * Save this guy in case anyone else wants it.
         */
        $container->set(tubepress_api_boot_BootSettingsInterface::_, $this->_helperSettingsFileReader);

        /**
         * Remember that we booted.
         */
        self::$_SERVICE_CONTAINER = $container;
    }

    private function _06_recordFinishTime()
    {
        if (!$this->_logger->isEnabled()) {

            return;
        }

        $now = microtime(true);

        $this->_logger->debug(sprintf('Boot completed in %f milliseconds',
            (($now - $this->_startTime) * 1000.0)));
    }

    private function _07_finishLoggingJobs()
    {
        $serviceContainer = self::$_SERVICE_CONTAINER;

        $serviceContainer->set('tubepress_impl_log_BootLogger', $this->_logger);
    }

    private function _08_freeMemory()
    {
        if (!$this->_helperSettingsFileReader->isClassLoaderEnabled()) {

            spl_autoload_unregister(array($this->_classLoader, 'loadClass'));
            unset($this->_classLoader);
        }

        unset($this->_helperSettingsFileReader);
        unset($this->_helperContainerSupplier);
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
     * @param ehough_pulsar_ComposerClassLoader $classloader The classloader.
     *
     * @internal
     */
    public function ___setClassLoader(ehough_pulsar_ComposerClassLoader $classloader)
    {
        $this->_classLoader = $classloader;
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
        $this->_logger = $logger;
    }
}