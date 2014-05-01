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
     * @var tubepress_impl_log_MemoryBufferLogger
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
     * @var tubepress_spi_boot_SettingsFileReaderInterface
     */
    private $_bootHelperSettingsFileReader;

    /**
     * @var tubepress_spi_boot_secondary_SecondaryBootstrapperInterface
     */
    private $_secondaryBootstrapper;

    /**
     * Performs TubePress-wide initialization.
     *
     * @throws Exception If an error was encountered during boot.
     *
     * @return tubepress_api_ioc_ContainerInterface
     */
    public function boot()
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
         * Read settings file.
         */
        $this->_04_readSettingsFile();

        /**
         * Build the secondary ...
         */
        $this->_05_buildSecondaryBootstrapper();

        /**
         * ... and run it.
         */
        $this->_06_performSecondaryBoot();

        /**
         * Record finish time.
         */
        $this->_07_recordFinishTime();

        /**
         * Flush the log if we need to.
         */
        $this->_08_finishLoggingJobs();

        /**
         * Free up some memory.
         */
        $this->_09_freeMemory();
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
            $this->_logger    = new tubepress_impl_log_MemoryBufferLogger($loggingRequested);
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

    private function _04_readSettingsFile()
    {
        if (!isset($this->_bootHelperSettingsFileReader)) {

            $this->_bootHelperSettingsFileReader = new tubepress_impl_boot_SettingsFileReader();
        }
    }

    private function _05_buildSecondaryBootstrapper()
    {
        if (isset($this->_secondaryBootstrapper)) {

            return;
        }

        if ($this->_canBootFromCache()) {

            if ($this->_logger->isEnabled()) {

                $this->_logger->debug('We can boot from cache. Excellent!');
            }

            $this->_secondaryBootstrapper = new tubepress_impl_boot_secondary_CachedSecondaryBootstrapper(

                $this->_logger->isEnabled()
            );

        } else {

            if ($this->_logger->isEnabled()) {

                $this->_logger->debug('We cannot boot from cache. Will perform a full boot instead.');
            }

            $finderFactory       = new ehough_finder_FinderFactory();
            $urlFactory          = new tubepress_addons_puzzle_impl_url_UrlFactory();
            $environmentDetector = new tubepress_addons_coreservices_impl_environment_Environment($urlFactory);

            $this->_secondaryBootstrapper = new tubepress_impl_boot_secondary_UncachedSecondaryBootstrapper(

                $this->_logger->isEnabled(),
                new tubepress_impl_boot_secondary_ClassLoaderPrimer(),
                new tubepress_impl_addon_AddonFinder($finderFactory, $environmentDetector, $urlFactory),
                new tubepress_impl_boot_secondary_IocCompiler()
            );
        }
    }

    private function _06_performSecondaryBoot()
    {
        $container = $this->_secondaryBootstrapper->getServiceContainer(

            $this->_bootHelperSettingsFileReader,
            $this->_classLoader
        );

        if ($this->_bootHelperSettingsFileReader->isClassLoaderEnabled()) {

            $this->_classLoader->addToClassMap($container->getParameter('classMap'));
        }

        /**
         * Keep this around for later.
         */
        $container->set('tubepress.settingsFileReader', $this->_bootHelperSettingsFileReader);

        /**
         * Remember that we booted.
         */
        self::$_SERVICE_CONTAINER = $container;
    }

    private function _07_recordFinishTime()
    {
        if (!$this->_logger->isEnabled()) {

            return;
        }

        $now = microtime(true);

        $this->_logger->debug(sprintf('Boot completed in %f milliseconds',
            (($now - $this->_startTime) * 1000.0)));
    }

    private function _08_finishLoggingJobs()
    {
        $serviceContainer = self::$_SERVICE_CONTAINER;
        $context          = $serviceContainer->get(tubepress_api_options_ContextInterface::_);
        $loggingEnabled   = $context->get(tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $loggingRequested = $this->_logger->isEnabled();
        $status           = $loggingEnabled && $loggingRequested;

        if ($status) {

            $finalLogger = new tubepress_impl_log_HtmlLogger(
                new ehough_epilog_Logger('TubePress'),
                new ehough_epilog_formatter_LineFormatter("[%datetime%] [%level_name%] %message%")
            );
            $this->_logger->flushTo($finalLogger);

        } else {

            $finalLogger = $this->_logger;
        }

        $this->_logger->disable();

        $serviceContainer->set(tubepress_api_log_LoggerInterface::_, $finalLogger);
    }

    private function _09_freeMemory()
    {
        unset($this->_bootHelperAddonBooter);
        unset($this->_bootHelperAddonDiscoverer);
        unset($this->_bootHelperSettingsFileReader);
        unset($this->_bootHelperClassLoadingHelper);
        unset($this->_bootHelperIocHelper);
    }

    public function _canBootFromCache()
    {
        if ($this->_logger->isEnabled()) {

            $this->_logger->debug('Determining if we can boot from the cache.');
        }

        if (!$this->_bootHelperSettingsFileReader->isContainerCacheEnabled()) {

            if ($this->_logger->isEnabled()) {

                $this->_logger->debug('Boot cache is disabled by user settings.php');
            }

            return false;
        }

        if (class_exists('TubePressServiceContainer', false)) {

            return true;
        }

        $file = $this->_bootHelperSettingsFileReader->getCachedContainerStoragePath();;

        if (!is_readable($file)) {

            if ($this->_logger->isEnabled()) {

                $this->_logger->debug(sprintf('%s is not a readable file.', $file));
            }

            return false;
        }

        if ($this->_logger->isEnabled()) {

            $this->_logger->debug(sprintf('%s is a readable file. Now including it.', $file));
        }

        /** @noinspection PhpIncludeInspection */
        require $file;

        $iocContainerHit = class_exists('TubePressServiceContainer', false);

        if ($this->_logger->isEnabled()) {

            if ($iocContainerHit) {

                $this->_logger->debug(sprintf('IOC container found in cache? %s', $iocContainerHit ? 'yes' : 'no'));
            }
        }

        return $iocContainerHit;
    }



    /***********************************************************************************************************
     *** TESTING FUNCTIONS *************************************************************************************
     **********************************************************************************************************/

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_spi_boot_SettingsFileReaderInterface $bcsi The settings file interface.
     *
     * @internal
     */
    public function ___setSettingsFileReader(tubepress_spi_boot_SettingsFileReaderInterface $bcsi)
    {
        $this->_bootHelperSettingsFileReader = $bcsi;
    }

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_spi_boot_secondary_SecondaryBootstrapperInterface $sbi The secondary bootstrapper.
     *
     * @internal
     */
    public function ___setSecondaryBootstrapper(tubepress_spi_boot_secondary_SecondaryBootstrapperInterface $sbi)
    {
        $this->_secondaryBootstrapper = $sbi;
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
     * @param tubepress_impl_log_MemoryBufferLogger $logger
     *
     * @internal
     */
    public function ___setTemporaryLogger(tubepress_impl_log_MemoryBufferLogger $logger)
    {
        $this->_logger = $logger;
    }
}