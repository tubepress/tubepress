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
 * and somewhat delicate process. Read carefully!
 */
class tubepress_impl_boot_PrimaryBootstrapper
{
    /**
     * @var boolean Did we already boot?
     */
    private static $_alreadyBooted = false;

    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var tubepress_impl_log_TubePressLoggingHandler
     */
    private $_loggingHandler;

    /**
     * @var bool Convenience variable for just this class.
     */
    private $_shouldLog = true;

    /**
     * @var tubepress_impl_ioc_CoreIocContainer The IoC container.
     */
    private $_iocContainer = null;

    /**
     * @var array
     */
    private $_addons = array();

    /**
     * @var float
     */
    private $_startTime;

    /**
     * @var ehough_pulsar_ComposerClassLoader
     */
    private $_classLoader;

    /**
     * @var tubepress_spi_boot_AddonBooter
     */
    private $_bootHelperAddonBooter;

    /**
     * @var tubepress_spi_boot_AddonDiscoverer
     */
    private $_bootHelperAddonDiscoverer;

    /**
     * @var tubepress_spi_boot_BootConfigService
     */
    private $_bootHelperBootConfigService;

    /**
     * @var tubepress_spi_boot_ClassLoadingHelper
     */
    private $_bootHelperClassLoadingHelper;

    /**
     * @var tubepress_spi_boot_IocContainerHelper
     */
    private $_bootHelperIocHelper;

    /**
     * Performs TubePress-wide initialization.
     *
     * @return boolean True if boot completed normally, false otherwise.
     */
    public final function boot()
    {
        /**
         * Don't boot twice!
         */
        if (self::$_alreadyBooted) {

            return true;
        }

        /**
         * Setup initial class loader.
         */
        $this->_01_buildInitialClassLoader();

        /*
         * Setup basic logging facilities.
         */
        $this->_02_configureLogging();

        /**
         * Record start time.
         */
        $this->_03_recordStartTime();

        try {

            $this->_04_buildInitialIocContainer();

            $this->_05_primeClassMap();

            $this->_06_configureLoggingForWordPress();

            $this->_07_discoverAddons();

            $this->_08_registerAddonClassHints();

            $this->_09_compileIocContainer();

            $this->_10_bootAddons();

            $this->_11_dispatchBootCompleteEvent();

            $this->_12_flushLogIfNeeded();

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->error('Caught exception while booting: '.  $e->getMessage());

                //flush out log statements
                $this->_loggingHandler->setStatus(true);
            }

            return false;
        }

        /**
         * Remember that we booted.
         */
        self::$_alreadyBooted = true;

        /**
         * Record finish time.
         */
        $this->_13_recordFinishTime();

        /**
         * Free up some memory.
         */
        $this->_14_freeMemory();

        return true;
    }

    private function _01_buildInitialClassLoader()
    {
        if (! class_exists('ehough_pulsar_ComposerClassLoader')) {

            require_once TUBEPRESS_ROOT . '/vendor/ehough/pulsar/src/main/php/ehough/pulsar/ComposerClassLoader.php';
        }

        $this->_classLoader = new ehough_pulsar_ComposerClassLoader(TUBEPRESS_ROOT . '/vendor/');
        $this->_classLoader->register();

        $bootStrapClassMap = require_once TUBEPRESS_ROOT . '/src/main/php/scripts/classmaps/bootstrap.php';

        $this->_classLoader->addToClassMap($bootStrapClassMap);
    }

    private function _02_configureLogging()
    {
        /*
         * All loggers will share this handler. This lets us control it nicely.
         */
        $loggingHandler   = new tubepress_impl_log_TubePressLoggingHandler();
        $loggingRequested = isset($_GET['tubepress_debug']) && strcasecmp($_GET['tubepress_debug'], 'true') === 0;

        if ($loggingRequested) {

            $loggingHandler->setLevel(ehough_epilog_Logger::DEBUG);

        } else {

            $loggingHandler->setLevel(ehough_epilog_Logger::EMERGENCY);
        }

        $this->_shouldLog = $loggingRequested;

        ehough_epilog_LoggerFactory::setHandlerStack(array($loggingHandler));

        $this->_logger = ehough_epilog_LoggerFactory::getLogger('TubePress Bootstrapper');

        $this->_loggingHandler = $loggingHandler;
    }

    private function _03_recordStartTime()
    {
        if ($this->_shouldLog) {

            /**
             * Keep track of how long this takes.
             */
            $this->_startTime = microtime(true);
        }
    }

    private function _04_buildInitialIocContainer()
    {
        if (!isset($this->_iocContainer)) {

            $this->_iocContainer = new tubepress_impl_ioc_CoreIocContainer();

            tubepress_impl_patterns_sl_ServiceLocator::setIocContainer($this->_iocContainer);
        }
    }

    private function _05_primeClassMap()
    {
        $classLoadingHelper = tubepress_impl_patterns_sl_ServiceLocator::getBootHelperClassLoadingHelper();

        $classLoadingHelper->prime($this->_classLoader);
    }

    private function _06_configureLoggingForWordPress()
    {
        $envDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        /* WordPress likes to keep control of the output */
        if ($envDetector->isWordPress()) {

            ob_start();
        }
    }

    private function _07_discoverAddons()
    {
        $discoverer = tubepress_impl_patterns_sl_ServiceLocator::getBootHelperAddonDiscoverer();

        $this->_addons = $discoverer->findAddons();
    }

    private function _08_registerAddonClassHints()
    {
        $classLoadingHelper = tubepress_impl_patterns_sl_ServiceLocator::getBootHelperClassLoadingHelper();

        $classLoadingHelper->addClassHintsForAddons($this->_addons, $this->_classLoader);
    }

    private function _09_compileIocContainer()
    {
        $helper = tubepress_impl_patterns_sl_ServiceLocator::getBootHelperIocContainer();

        $helper->compile($this->_iocContainer, $this->_addons);
    }

    private function _10_bootAddons()
    {
        $addonBooter = tubepress_impl_patterns_sl_ServiceLocator::getBootHelperAddonBooter();

        $addonBooter->boot($this->_addons);
    }

    private function _11_dispatchBootCompleteEvent()
    {
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::BOOT_COMPLETE);
    }

    private function _12_flushLogIfNeeded()
    {
        $context          = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $hrps             = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $loggingEnabled   = $context->get(tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $loggingRequested = $hrps->hasParam('tubepress_debug') && $hrps->getParamValue('tubepress_debug') === true;
        $status           = $loggingEnabled && $loggingRequested;

        $this->_loggingHandler->setStatus($status);
        $this->_shouldLog = $status;
    }

    private function _13_recordFinishTime()
    {
        if (!$this->_shouldLog) {

            return;
        }

        $now = microtime(true);

        $this->_logger->debug(sprintf('Boot completed in %f milliseconds',
            (($now - $this->_startTime) * 1000.0)));
    }

    private function _14_freeMemory()
    {
        unset($this->_addons);
        unset($this->_bootHelperAddonBooter);
        unset($this->_bootHelperAddonDiscoverer);
        unset($this->_bootHelperBootConfigService);
        unset($this->_bootHelperClassLoadingHelper);
        unset($this->_bootHelperIocHelper);
        unset($this->_iocContainer);
        unset($this->_logger);
    }

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_api_ioc_ContainerInterface $iocContainer The IoC container.
     *
     * @internal
     */
    public final function setIocContainer(tubepress_api_ioc_ContainerInterface $iocContainer)
    {
        $this->_iocContainer = $iocContainer;
    }
}
