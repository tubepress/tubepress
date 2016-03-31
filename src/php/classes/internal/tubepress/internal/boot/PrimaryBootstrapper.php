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
 * Performs TubePress-wide initialization. This is a complicated
 * and somewhat delicate process. Take your time and read carefully!
 */
class tubepress_internal_boot_PrimaryBootstrapper
{
    const CONTAINER_PARAM_BOOT_ARTIFACTS = 'boot-artifacts';

    /**
     * @var tubepress_api_ioc_ContainerInterface
     */
    private static $_SERVICE_CONTAINER;

    /**
     * @var Exception
     */
    private static $_BOOT_EXCEPTION;

    /**
     * @var tubepress_internal_logger_BootLogger
     */
    private $_bootLogger;

    /**
     * @var float
     */
    private $_startTime;

    /**
     * @var tubepress_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var tubepress_internal_boot_helper_ContainerSupplier
     */
    private $_containerSupplier;

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
        $miniMap = array(
            '\Symfony\Component\DependencyInjection\ContainerInterface' =>
                TUBEPRESS_ROOT . '/vendor/symfony/dependency-injection/ContainerInterface.php',
            '\Symfony\Component\DependencyInjection\IntrospectableContainerInterface' =>
                TUBEPRESS_ROOT . '/vendor/symfony/dependency-injection/IntrospectableContainerInterface.php',
            '\Symfony\Component\DependencyInjection\ResettableContainerInterface' =>
                TUBEPRESS_ROOT . '/vendor/symfony/dependency-injection/ResettableContainerInterface.php',
            '\Symfony\Component\DependencyInjection\Container' =>
                TUBEPRESS_ROOT . '/vendor/symfony/dependency-injection/Container.php',
        );

        foreach ($miniMap as $classname => $absPath) {

            $isInterface      = strpos($absPath, 'Interface.php') !== false;
            $includeInterface = $isInterface && !interface_exists($classname, false);
            $includeClass     = !$isInterface && !class_exists($classname, false);

            if ($includeInterface || $includeClass) {

                /** @noinspection PhpIncludeInspection */
                require $absPath;
            }
        }

        $classConcatenationPath = TUBEPRESS_ROOT . '/src/php/scripts/classloading/classes.php';

        /**
         * This should be the common case in production.
         */
        if (!interface_exists('tubepress_api_ioc_ContainerInterface', false) && file_exists($classConcatenationPath)) {

            /** @noinspection PhpIncludeInspection */
            require $classConcatenationPath;
        }
    }

    private function _02_buildBootLogger()
    {
        if (!isset($this->_bootLogger)) {

            $loggingRequested  = isset($_GET['tubepress_debug']) && strcasecmp($_GET['tubepress_debug'], 'true') === 0;
            $this->_bootLogger = new tubepress_internal_logger_BootLogger($loggingRequested);

            if ($loggingRequested) {

                $this->_logDebug(sprintf('Hello! Thanks for using TubePress version <code>%s</code>', TUBEPRESS_VERSION));
            }
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
            phpinfo(INFO_GENERAL|INFO_CONFIGURATION|INFO_MODULES);
            $phpInfo = ob_get_contents();
            ob_end_clean();
            $phpInfo = base64_encode($phpInfo);

            $this->_logDebug(sprintf('Check the HTML source for additional debug info. <span style="display: none" class="php-debug">%s</span>', $phpInfo));
        }
    }
    
    private function _04_buildHelperServices()
    {
        if (!isset($this->_bootSettings)) {

            $this->_bootSettings = new tubepress_internal_boot_BootSettings($this->_bootLogger, new tubepress_url_impl_puzzle_UrlFactory($_SERVER));
        }

        if (!isset($this->_containerSupplier)) {

            $this->_containerSupplier = new tubepress_internal_boot_helper_ContainerSupplier($this->_bootLogger, $this->_bootSettings);
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
        if (!$this->_bootSettings->isClassLoaderEnabled()) {

            return;
        }

        $expectedPath = $this->_bootSettings->getPathToSystemCacheDirectory() . DIRECTORY_SEPARATOR . 'classmap.php';
        $debugEnabled = $this->_bootLogger->isEnabled();

        if ($debugEnabled) {

            $this->_logDebug(sprintf('Attempting to include classmap from <code>%s</code>', $expectedPath));
        }

        if (!is_readable($expectedPath)) {

            if ($debugEnabled) {

                $this->_logDebug(sprintf('<code>%s</code> is not readable. That\'s not great.', $expectedPath));
            }

            return;
        }

        if ($debugEnabled) {

            $this->_logDebug(sprintf('<code>%s</code> is readable.', $expectedPath));
        }

        if (!class_exists('Symfony\Component\ClassLoader\MapClassLoader', false)) {

            require TUBEPRESS_ROOT . '/vendor/symfony/class-loader/MapClassLoader.php';
        }

        $classMap       = require $expectedPath;
        $mapClassLoader = new \Symfony\Component\ClassLoader\MapClassLoader($classMap);

        $mapClassLoader->register();

        if ($debugEnabled) {

            $this->_logDebug(sprintf('Successfully loaded a map of <code>%d</code> classes from <code>%s</code>.',

                count($classMap),
                $expectedPath
            ));
        }
    }

    private function _07_recordFinishTime()
    {
        if (!$this->_bootLogger->isEnabled()) {

            return;
        }

        $now = microtime(true);

        $this->_logDebug(
            sprintf(
                'Boot completed in <code>%f</code> milliseconds. Actual performance will be better when debugging is not active.',
                ($now - $this->_startTime) * 1000.0
            )
        );

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
        if (!class_exists('\Symfony\Component\Filesystem\Filesystem', false)) {

            require TUBEPRESS_ROOT . '/vendor/symfony/filesystem/Filesystem.php';
            require TUBEPRESS_ROOT . '/vendor/symfony/filesystem/Exception/ExceptionInterface.php';
            require TUBEPRESS_ROOT . '/vendor/symfony/filesystem/Exception/IOExceptionInterface.php';
            require TUBEPRESS_ROOT . '/vendor/symfony/filesystem/Exception/IOException.php';
            require TUBEPRESS_ROOT . '/vendor/symfony/filesystem/Exception/FileNotFoundException.php';
        }

        $dir        = $this->_bootSettings->getPathToSystemCacheDirectory();
        $filesystem = new \Symfony\Component\Filesystem\Filesystem();

        if ($this->_bootLogger->isEnabled()) {

            $this->_logDebug(sprintf('System cache clear requested. Attempting to recursively delete <code>%s</code>', $dir));
        }

        $filesystem->remove($dir);
        $filesystem->mkdir($dir, 0755);
    }

    private function _logDebug($msg)
    {
        $this->_bootLogger->debug(sprintf('(Primary Bootstrapper) %s', $msg));
    }

    public function _handleFatalError()
    {
        $lastError = error_get_last();

        if (!is_array($lastError) || !$this->_bootLogger->isEnabled()) {

            //no error or logging not enabled
            return;
        }

        if (!class_exists('tubepress_internal_boot_helper_FatalErrorHandler', false)) {
            
            require __DIR__ . '/helper/FatalErrorHandler.php';
        }
        
        $handler = new tubepress_internal_boot_helper_FatalErrorHandler();
        $handler->onFatalError($this->_bootLogger, $lastError);
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
        $this->_bootSettings = $bcsi;
    }

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_internal_boot_helper_ContainerSupplier $sbi The container supplier.
     *
     * @internal
     */
    public function ___setContainerSupplier(tubepress_internal_boot_helper_ContainerSupplier $sbi)
    {
        $this->_containerSupplier = $sbi;
    }

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_internal_logger_BootLogger $logger
     *
     * @internal
     */
    public function ___setTemporaryLogger(tubepress_internal_logger_BootLogger $logger)
    {
        $this->_bootLogger = $logger;
    }
}