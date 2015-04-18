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
 * Performs TubePress-wide initialization. This is a complicated
 * and somewhat delicate process. Take your time and read carefully!
 */
class tubepress_platform_impl_boot_PrimaryBootstrapper
{
    const CONTAINER_PARAM_BOOT_ARTIFACTS = 'boot-artifacts';

    /**
     * @var tubepress_platform_api_ioc_ContainerInterface
     */
    private static $_SERVICE_CONTAINER;

    /**
     * @var Exception
     */
    private static $_BOOT_EXCEPTION;

    /**
     * @var tubepress_platform_impl_log_BootLogger
     */
    private $_bootLogger;

    /**
     * @var float
     */
    private $_startTime;

    /**
     * @var tubepress_platform_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var tubepress_platform_impl_boot_helper_ContainerSupplier
     */
    private $_containerSupplier;

    /**
     * Performs TubePress-wide initialization.
     *
     * @throws Exception If an error was encountered during boot.
     *
     * @return tubepress_platform_api_ioc_ContainerInterface
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
         * Register the fatal error handler.
         */
        $this->_00_registerFatalErrorHandler();

        /**
         * Setup initial class loader.
         */
        $this->_01_loadMinimalClasses();

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

    private function _00_registerFatalErrorHandler()
    {
        register_shutdown_function(array($this, '_handleFatalError'));
    }

    private function _01_loadMinimalClasses()
    {
        $classConcatenationPath = TUBEPRESS_ROOT . '/src/platform/scripts/classloading/classes.php';

        /**
         * This should be the common case in production.
         */
        if (!interface_exists('tubepress_platform_api_ioc_ContainerInterface', false) && file_exists($classConcatenationPath)) {

            /** @noinspection PhpIncludeInspection */
            require $classConcatenationPath;
        }
    }

    private function _02_buildBootLogger()
    {
        if (!isset($this->_bootLogger)) {

            $loggingRequested  = isset($_GET['tubepress_debug']) && strcasecmp($_GET['tubepress_debug'], 'true') === 0;
            $this->_bootLogger = new tubepress_platform_impl_log_BootLogger($loggingRequested);
        }
    }

    private function _03_recordStartTime()
    {
        if ($this->_bootLogger->isEnabled()) {

            /**
             * Keep track of how long this takes.
             */
            $this->_startTime = microtime(true);

            /**
             * Add a poorly obscured copy of phpinfo() to the output. We encode it just in case
             * search engines try to pick it up. Weaksauce, I know.
             */
            ob_start();
            phpinfo();
            $phpInfo = ob_get_contents();
            ob_end_clean();
            $phpInfo = base64_encode($phpInfo);

            $this->_bootLogger->debug(sprintf('<span style="display: none" class="php-debug">%s</span>', $phpInfo));
        }
    }
    
    private function _04_buildHelperServices()
    {
        if (!isset($this->_bootSettings)) {

            $this->_bootSettings = new tubepress_platform_impl_boot_BootSettings($this->_bootLogger, new tubepress_platform_impl_url_puzzle_UrlFactory($_SERVER));
        }

        if (!isset($this->_containerSupplier)) {

            $this->_containerSupplier = new tubepress_platform_impl_boot_helper_ContainerSupplier(
                
                $this->_bootLogger,
                $this->_bootSettings
            );
        }
    }

    private function _05_loadServiceContainer()
    {
        if ($this->_bootSettings->shouldClearCache()) {

            $this->_clearSystemCache();
        }

        self::$_SERVICE_CONTAINER = $this->_containerSupplier->getServiceContainer();
    }

    private function _06_registerClassLoaderIfRequested()
    {
        $container = self::$_SERVICE_CONTAINER;

        if (!$this->_bootSettings->isClassLoaderEnabled()) {

            return;
        }

        if ($container->hasParameter(self::CONTAINER_PARAM_BOOT_ARTIFACTS)) {

            $bootArtifacts = $container->getParameter(self::CONTAINER_PARAM_BOOT_ARTIFACTS);

            if (!is_array($bootArtifacts) || !isset($bootArtifacts['classloading']) || !is_array($bootArtifacts['classloading'])) {

                return;
            }

            $classLoadingArtifacts = $bootArtifacts['classloading'];

            if (isset($classLoadingArtifacts['map'])
                && is_array($classLoadingArtifacts['map'])) {

                if (!class_exists('ehough_pulsar_MapClassLoader', false)) {

                    require TUBEPRESS_ROOT . '/vendor/ehough/pulsar/src/main/php/ehough/pulsar/MapClassLoader.php';
                }

                $mapClassLoader = new ehough_pulsar_MapClassLoader($classLoadingArtifacts['map']);

                $mapClassLoader->register();
            }
        }
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
         * @var $realLogger tubepress_platform_api_log_LoggerInterface
         */
        $realLogger = self::$_SERVICE_CONTAINER->get(tubepress_platform_api_log_LoggerInterface::_);

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
        unset($this->_bootSettings);
        unset($this->_containerSupplier);
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

    private function _clearSystemCache()
    {
        $dir       = $this->_bootSettings->getPathToSystemCacheDirectory();
        $shouldLog = $this->_bootLogger->isEnabled();

        if ($shouldLog) {

            $this->_bootLogger->debug(sprintf('System cache clear requested. Attempting to recursively delete %s', $dir));
        }

        $this->_recursivelyDeleteDirectory($dir, $this->_bootLogger, $shouldLog);
    }

    private function _recursivelyDeleteDirectory($dir, tubepress_platform_api_log_LoggerInterface $logger, $shouldLog)
    {
        if (!is_dir($dir)) {

            return;
        }

        $objects = scandir($dir);

        if ($shouldLog) {

            $logger->debug(sprintf('Found %d objects in %s', count($objects), $dir));
        }

        foreach ($objects as $object) {

            if ($object == '.' || $object == '..') {

                continue;
            }

            $path = $dir . DIRECTORY_SEPARATOR . $object;

            if (filetype($path) == 'dir') {

                if ($shouldLog) {

                    $logger->debug(sprintf('Recursing inside %s to delete it', $path));
                }

                $this->_recursivelyDeleteDirectory($path, $logger, $shouldLog);

            } else  {

                if ($shouldLog) {

                    $logger->debug(sprintf('Attempting to delete %s', $path));
                }

                $success = unlink($path);

                if ($shouldLog) {

                    if ($success === true) {

                        $logger->debug(sprintf('Successfully deleted %s', $path));

                    } else {

                        $logger->error(sprintf('Could not delete %s', $path));
                    }
                }
            }

        }

        reset($objects);

        if ($shouldLog) {

            $logger->debug(sprintf('Attempting to delete directory %s', $dir));
        }

        $success = rmdir($dir);

        if ($shouldLog) {

            if ($success === true) {

                $logger->debug(sprintf('Successfully deleted directory %s', $dir));

            } else {

                $logger->error(sprintf('Could not delete directory %s', $dir));
            }
        }
    }

    public function _handleFatalError()
    {
        if (!class_exists('tubepress_platform_impl_boot_helper_FatalErrorHandler', false)) {
            
            require dirname(__FILE__) . '/helper/FatalErrorHandler.php';
        }
        
        $handler = new tubepress_platform_impl_boot_helper_FatalErrorHandler();
        $handler->onFatalError();
    }

    /***********************************************************************************************************
     *** TESTING FUNCTIONS *************************************************************************************
     **********************************************************************************************************/

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_platform_api_boot_BootSettingsInterface $bcsi The settings file interface.
     *
     * @internal
     */
    public function ___setSettingsFileReader(tubepress_platform_api_boot_BootSettingsInterface $bcsi)
    {
        $this->_bootSettings = $bcsi;
    }

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_platform_impl_boot_helper_ContainerSupplier $sbi The container supplier.
     *
     * @internal
     */
    public function ___setContainerSupplier(tubepress_platform_impl_boot_helper_ContainerSupplier $sbi)
    {
        $this->_containerSupplier = $sbi;
    }

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_platform_impl_log_BootLogger $logger
     *
     * @internal
     */
    public function ___setTemporaryLogger(tubepress_platform_impl_log_BootLogger $logger)
    {
        $this->_bootLogger = $logger;
    }
}