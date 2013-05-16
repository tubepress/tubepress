<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Performs TubePress-wide initialization.
 */
class tubepress_impl_bootstrap_TubePressBootstrapper
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
     * @var ehough_pulsar_ComposerClassLoader The classloader.
     */
    private $_classLoader;

    /**
     * @var tubepress_impl_ioc_CoreIocContainer The IoC container.
     */
    private $_iocContainer = null;

    /**
     * @var array
     */
    private $_bootConfig = array();

    /**
     * @var bool
     */
    private $_iocContainerNeedsCompilation = true;

    /**
     * @var array
     */
    private $_addons = array();

    /**
     * Performs TubePress-wide initialization.
     *
     * @return null
     */
    public final function boot()
    {
        /* don't boot twice! */
        if (self::$_alreadyBooted) {

            return;
        }

        /**
         * Setup initial class loader.
         */
        $this->_01_buildClassLoader();

        /*
         * Setup basic logging facilities.
         */
        $this->_02_configureLogging();

        /**
         * Keep track of how long this takes.
         */
        $then = microtime(true);

        try {

            $this->_doBoot();

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->error('Caught exception while booting: '.  $e->getMessage());

                //flush out log statements
                $this->_loggingHandler->setStatus(true);
            }
        }

        /* remember that we booted. */
        self::$_alreadyBooted = true;

        if ($this->_shouldLog) {

            $now = microtime(true);

            $this->_logger->debug(sprintf('Boot completed in %f milliseconds',
                (($now - $then) * 1000.0)));
        }
    }

    /**
     * This is here strictly for testing :/
     *
     * @param tubepress_api_ioc_ContainerInterface $iocContainer The IoC container.
     */
    public final function setIocContainer(tubepress_api_ioc_ContainerInterface $iocContainer)
    {
        $this->_iocContainer = $iocContainer;
    }

    private function _doBoot()
    {
        $this->_03_addFullClassMap();

        $this->_04_buildInitialIocContainer();

        $this->_05_configureLoggingForWordPress();

        if ($this->_shouldLog) {

            $this->_logger->debug('Booting!');
        }

        $this->_06_readBootConfig();

        $this->_07_discoverAddons();

        $this->_08_registerAddonClassHints();

        $this->_09_tryToGetIocContainerFromCache();

        $this->_10_compileIocContainerIfNeeded();

        $this->_11_bootAddons();

        $this->_12_dispatchBootCompleteEvent();

        /**
         * Now that we have a storage manager, let's enable or disable logging permanently.
         */
        $this->_13_flushLogIfNeeded();
    }

    private function _01_buildClassLoader()
    {
        if (! class_exists('ehough_pulsar_ComposerClassLoader')) {

            require_once TUBEPRESS_ROOT . '/vendor/ehough/pulsar/src/main/php/ehough/pulsar/ComposerClassLoader.php';
        }
        $this->_classLoader = new ehough_pulsar_ComposerClassLoader(TUBEPRESS_ROOT . '/vendor/');
        $this->_classLoader->register();

        $epilogPrefix = TUBEPRESS_ROOT . '/vendor/ehough/epilog/src/main/php/ehough/epilog';

        $this->_classLoader->addToClassMap(array(

            'ehough_epilog_formatter_FormatterInterface'      => $epilogPrefix . '/formatter/FormatterInterface.php',
            'ehough_epilog_formatter_LineFormatter'           => $epilogPrefix . '/formatter/LineFormatter.php',
            'ehough_epilog_formatter_NormalizerFormatter'     => $epilogPrefix . '/formatter/NormalizerFormatter.php',
            'ehough_epilog_handler_AbstractHandler'           => $epilogPrefix . '/handler/AbstractHandler.php',
            'ehough_epilog_handler_AbstractProcessingHandler' => $epilogPrefix . '/handler/AbstractProcessingHandler.php',
            'ehough_epilog_handler_HandlerInterface'          => $epilogPrefix . '/handler/HandlerInterface.php',
            'ehough_epilog_handler_NullHandler'               => $epilogPrefix . '/handler/NullHandler.php',
            'ehough_epilog_LoggerFactory'                     => $epilogPrefix . '/LoggerFactory.php',
            'ehough_epilog_Logger'                            => $epilogPrefix . '/Logger.php',
            'ehough_epilog_psr_AbstractLogger'                => $epilogPrefix . '/psr/AbstractLogger.php',
            'ehough_epilog_psr_InvalidArgumentException'      => $epilogPrefix . '/psr/InvalidArgumentException.php',
            'ehough_epilog_psr_LoggerAwareInterface'          => $epilogPrefix . '/psr/LoggerAwareInterface.php',
            'ehough_epilog_psr_LoggerInterface'               => $epilogPrefix . '/psr/LoggerInterface.php',

            'tubepress_impl_log_TubePressLoggingHandler' => TUBEPRESS_ROOT . '/src/main/php/classes/tubepress/impl/log/TubePressLoggingHandler.php',
        ));
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

            $loggingHandler->setLevel(ehough_epilog_Logger::WARNING);
        }

        $this->_shouldLog = $loggingRequested;

        ehough_epilog_LoggerFactory::setHandlerStack(array($loggingHandler));

        $this->_logger = ehough_epilog_LoggerFactory::getLogger('TubePress Bootstrapper');

        $this->_loggingHandler = $loggingHandler;
    }

    private function _03_addFullClassMap()
    {
        $classMapFile = TUBEPRESS_ROOT . '/src/main/php/scripts/classMap.php';

        if ($this->_shouldLog) {

            $this->_logger->debug('Now including classmap from ' . $classMapFile);
        }

        /** @noinspection PhpIncludeInspection */
        $classMap = require $classMapFile;

        $this->_classLoader->addToClassMap($classMap);

        if ($this->_shouldLog) {

            $this->_logger->debug('Done including classmap from ' . $classMapFile);
        }
    }

    private function _04_buildInitialIocContainer()
    {
        if (!isset($this->_iocContainer)) {

            $this->_iocContainer = new tubepress_impl_ioc_CoreIocContainer();
        }

        tubepress_impl_patterns_sl_ServiceLocator::setIocContainer($this->_iocContainer);
    }

    private function _05_configureLoggingForWordPress()
    {
        $envDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        /* WordPress likes to keep control of the output */
        if ($envDetector->isWordPress()) {

            ob_start();
        }
    }

    private function _06_readBootConfig()
    {
        $envDetector          = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $userContentDirectory = $envDetector->getUserContentDirectory();
        $configFileLocation   = $userContentDirectory . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'boot.json';

        if (!is_file($configFileLocation) || !is_readable($configFileLocation)) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('No readable config file at %s', $configFileLocation));
            }

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Attempting to read config from %s', $configFileLocation));
        }

        $contents = file_get_contents($configFileLocation);

        if ($contents === false) {

            if ($this->_shouldLog) {

                $this->_logger->warn(sprintf('Failed to read file contents of %s', $configFileLocation));
            }

            return;
        }

        $decoded = @json_decode($contents, true);

        if ($decoded === false || !is_array($decoded)) {

            if ($this->_shouldLog) {

                $this->_logger->warn(sprintf('Failed to parse %s', $configFileLocation));
            }
        }

        $this->_bootConfig = $decoded;
    }

    private function _07_discoverAddons()
    {
        if ($this->_discoverAddonsFromCache()) {

            return;
        }

        $this->_discoverAddonsFromFilesystem();

        if (!isset($this->_bootConfig['addons']['blacklist'])) {

            return;
        }

        $addonBlacklist = $this->_bootConfig['addons']['blacklist'];

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Add-on blacklist: %s', json_encode($addonBlacklist)));
        }

        $addonCount = count($this->_addons);

        for ($x = 0; $x < $addonCount; $x++) {

            /**
             * @var $addon tubepress_spi_addon_Addon
             */
            $addon     = $this->_addons[$x];
            $addonName = $addon->getName();

            if (in_array($addonName, $addonBlacklist)) {

                unset($this->_addons[$x]);
            }
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('After blacklist processing, we now have %d add-on(s)', count($this->_addons)));
        }

        $this->_tryToCacheAddons();
    }

    private function _discoverAddonsFromCache()
    {
        if (!$this->_isAddonCachingEnabled()) {

            return false;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug('Attempting to read add-ons from cache');
        }

        $path = $this->_calculateCacheFilePath('addons', 'serialized-addons.txt');

        if (!is_file($path) || !is_readable($path)) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Addon cache file (%s) is not a readable file', $path));
            }

            return false;
        }

        $contents = file_get_contents($path);

        if ($contents === false) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Could not read add-on cache file (%s)', $path));
            }

            return false;
        }

        $unserialized = @unserialize($contents);

        if ($unserialized === false || !is_array($unserialized)) {

            if ($this->_shouldLog) {

                $this->_logger->warn(sprintf('Could not unserialize add-on cache file (%s)', $path));
            }

            return false;
        }

        foreach ($unserialized as $addon) {

            if (!($addon instanceof tubepress_spi_addon_Addon)) {

                if ($this->_shouldLog) {

                    $this->_logger->warn(sprintf('Add-on cache file contains invalid data (%s)', $path));
                }

                return false;
            }
        }

        $this->_addons = $unserialized;

        return true;
    }

    private function _discoverAddonsFromFilesystem()
    {
        $addonDiscoverer = tubepress_impl_patterns_sl_ServiceLocator::getAddonDiscoverer();

        /* load add-ons */
        $systemAddons = $this->_findSystemAddons($addonDiscoverer);
        $userAddons   = $this->_findUserAddons($addonDiscoverer);
        $allAddons    = array_merge($systemAddons, $userAddons);
        $addOnCount   = count($allAddons);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found %d add-ons (%d system and %d user)',
                $addOnCount, count($systemAddons), count($userAddons)));
        }

        $this->_addons = $allAddons;
    }

    private function _tryToCacheAddons()
    {
        if (!$this->_isAddonCachingEnabled()) {

            if ($this->_shouldLog) {

                $this->_logger->debug('Since add-on caching is disabled, we will not attempt to cache add-ons.');
            }

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug('Attempting to cache add-ons...');
        }

        $serialized = @serialize($this->_addons);

        if ($serialized === false) {

            if ($this->_shouldLog) {

                $this->_logger->warn('Unable to serialize add-ons for cache');
            }

            return;
        }

        $path    = $this->_calculateCacheFilePath('addons', 'serialized-addons.txt');
        $written = file_put_contents($path, $serialized);

        if ($written === false) {

            if ($this->_shouldLog) {

                $this->_logger->warn(sprintf('Cannot serialize add-ons to %s since it is not a writable file', $path));
            }
        }
    }

    private function _findUserAddons(tubepress_spi_addon_AddonDiscoverer $discoverer)
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        $userContentDir = $environmentDetector->getUserContentDirectory();
        $userAddonsDir = $userContentDir . '/addons';

        return $this->_findAddonsInDirectory($userAddonsDir,
            $discoverer, true);
    }

    private function _findSystemAddons(tubepress_spi_addon_AddonDiscoverer $discoverer)
    {
        $coreAddons = $this->_findAddonsInDirectory(TUBEPRESS_ROOT . '/src/main/php/addons',
            $discoverer, true);

        usort($coreAddons, array($this, '_systemAddonSorter'));

        return $coreAddons;
    }

    private function _findAddonsInDirectory($directory, tubepress_spi_addon_AddonDiscoverer $discoverer, $recursive)
    {
        if ($recursive) {

            $addons = $discoverer->findAddonsInDirectory(realpath($directory));

        } else {

            $addons = $discoverer->findAddonsInDirectory(realpath($directory));
        }

        return $addons;
    }

    private function _isAddonCachingEnabled()
    {
        if (!isset($this->_bootConfig['cache']['addons']['enabled']) || !$this->_bootConfig['cache']['addons']['enabled']) {

            $toReturn = false;

        } else {

            $toReturn = true;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Add-on caching is%s enabled', $toReturn ? '' : ' not'));
        }

        return $toReturn;
    }

    private function _systemAddonSorter(tubepress_spi_addon_Addon $first, tubepress_spi_addon_Addon $second)
    {
        $firstName  = $first->getName();
        $secondName = $second->getName();

        /*
         * The core add-on always gets loaded first, the pro-core always last.
         */

        if ($firstName === 'tubepress-core-addon' || $secondName === 'tubepress-pro-core-addon') {

            return -1;
        }

        if ($firstName === 'tubepress-pro-core-addon' || $secondName === 'tubepress-core-addon') {

            return 1;
        }

        return 0;
    }

    private function _08_registerAddonClassHints()
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Now registering add-on class hints');
        }

        /**
         * Load classpaths.
         */
        $this->_registerAddonClasspaths($this->_addons);

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering add-on class hints. Now registering add-on IoC container extensions.');
        }
    }

    private function _registerAddonClasspaths(array $addons)
    {
        $index = 1;
        $count = count($addons);

        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        foreach ($addons as $addon) {

            $this->_registerPsr0PathsForAddon($addon, $index, $count);
            $this->_registerClassMapForAddon($addon, $index, $count);

            $index++;
        }
    }

    private function _registerClassMapForAddon(tubepress_spi_addon_Addon $addon, $index, $count)
    {
        $classMap = $addon->getClassMap();

        if (count($classMap) === 0) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Did not define a classmap',
                    $index, $count, $addon->getName()));
            }

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Adding classmap of size %d to classloader',
                $index, $count, $addon->getName(), count($classMap)));
        }

        $this->_classLoader->addToClassMap($classMap);
    }

    private function _registerPsr0PathsForAddon(tubepress_spi_addon_Addon $addon, $index, $count)
    {
        $classPaths = $addon->getPsr0ClassPathRoots();

        if (count($classPaths) === 0) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Did not define any PSR-0 classpaths',
                    $index, $count, $addon->getName()));
            }

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Adding %d PSR-0 path(s) to classloader',
                $index, $count, $addon->getName(), count($classPaths)));
        }

        foreach ($classPaths as $prefix => $path) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Registering %s => %s as a PSR-0 classpath',
                    $index, $count, $addon->getName(), $prefix, $path));
            }

            if ($prefix) {

                $this->_classLoader->registerPrefix($prefix, $path);
                $this->_classLoader->registerNamespace($prefix, $path);

            } else {

                $this->_classLoader->registerNamespaceFallback($path);
                $this->_classLoader->registerPrefixFallback($path);
            }
        }
    }

    private function _09_tryToGetIocContainerFromCache()
    {
        if (!$this->_isContainerCachingEnabled()) {

            return false;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug('Attempting to read service container from cache');
        }

        $path = $this->_calculateCacheFilePath('service-container', 'cached-service-container.txt');

        if (!is_file($path) || !is_readable($path)) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Service container cache file (%s) is not a readable file', $path));
            }

            return false;
        }

        include $path;

        if (!class_exists('TubePressServiceContainer')) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Could not read cached service container', $path));
            }

            return false;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Loaded cached container. Now attempting to instantiate', $path));
        }

        /** @noinspection PhpUndefinedClassInspection */
        $iconicContainer = new TubePressServiceContainer();

        $this->_iocContainer->setDelegateIconicContainer($iconicContainer);

        $this->_iocContainerNeedsCompilation = false;

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Successfully loaded cached container.', $path));
        }

        return true;
    }

    private function _isContainerCachingEnabled()
    {
        if (!isset($this->_bootConfig['cache']['service-container']['enabled']) || !$this->_bootConfig['cache']['service-container']['enabled']) {

            $toReturn = false;

        } else {

            $toReturn = true;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('IOC container caching is%s enabled', $toReturn ? '' : ' not'));
        }

        return $toReturn;
    }

    private function _10_compileIocContainerIfNeeded()
    {
        if (!$this->_iocContainerNeedsCompilation) {

            if ($this->_shouldLog) {

                $this->_logger->debug('IOC container does not need compilation. Good!');
            }

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug('IOC container needs compilation.');
        }

        /**
        * Load IOC container extensions.
        */
        $this->_registerIocContainerExtensions();

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering add-on IoC container extensions. Now registering add-on IoC compiler passes.');
        }

        /*
         * Load IOC compiler passes.
         */
        $this->_registerIocCompilerPasses();

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering add-on IoC compiler passes. Now compiling IoC container.');
        }

        /**
         * Compile all our services.
         */
        $this->_iocContainer->compile();

        if ($this->_shouldLog) {

            $this->_logger->debug('Done compiling IoC container.');
        }

        $this->_tryToCacheIocContainer();
    }

    private function _registerIocContainerExtensions()
    {
        $index = 1;
        $count = count($this->_addons);

        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        foreach ($this->_addons as $addon) {

            $extensions = $addon->getIocContainerExtensions();

            if (count($extensions) === 0) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Did not register any IoC container extensions',
                        $index, $count, $addon->getName()));
                }

                $index++;

                continue;
            }

            foreach ($extensions as $extension) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Will attempt to load %s as an IoC container extension',
                        $index, $count, $addon->getName(), $extension));
                }

                try {

                    $ref = new ReflectionClass($extension);

                    $this->_iocContainer->registerExtension($ref->newInstance());

                    if ($this->_shouldLog) {

                        $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Successfully loaded %s as an IoC container extension',
                            $index, $count, $addon->getName(), $extension));
                    }

                } catch (Exception $e) {

                    if ($this->_shouldLog) {

                        $this->_logger->warn(sprintf('(Add-on %d of %d: %s) Failed to load %s as an IoC container extension: %s',
                            $index, $count, $addon->getName(), $extension, $e->getMessage()));
                    }
                }
            }

            $index++;
        }
    }

    private function _registerIocCompilerPasses()
    {
        $index = 1;
        $count = count($this->_addons);

        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        foreach ($this->_addons as $addon) {

            $compilerPasses = $addon->getIocContainerCompilerPasses();

            if (count($compilerPasses) === 0) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Did not register any IoC compiler passes',
                        $index, $count, $addon->getName()));
                }

                $index++;

                continue;
            }

            foreach ($compilerPasses as $compilerPass) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Will attempt to load %s as an IoC compiler pass',
                        $index, $count, $addon->getName(), $compilerPass));
                }

                try {

                    $ref = new ReflectionClass($compilerPass);

                    $this->_iocContainer->addCompilerPass($ref->newInstance());

                    if ($this->_shouldLog) {

                        $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Successfully loaded %s as an IoC compiler pass',
                            $index, $count, $addon->getName(), $compilerPass));
                    }

                } catch (Exception $e) {

                    if ($this->_shouldLog) {

                        $this->_logger->warn(sprintf('(Add-on %d of %d: %s) Failed to load %s as an IoC compiler pass: %s',
                            $index, $count, $addon->getName(), $compilerPass, $e->getMessage()));
                    }
                }
            }

            $index++;
        }
    }

    private function _tryToCacheIocContainer()
    {
        if (!$this->_isContainerCachingEnabled()) {

            if ($this->_shouldLog) {

                $this->_logger->debug('Since IOC container caching is disabled, we will not attempt to cache container.');
            }

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->debug('Attempting to cache IOC container...');
        }

        $dumped = null;

        try {

            $dumper = new ehough_iconic_dumper_PhpDumper($this->_iocContainer->getDelegateIconicContainerBuilder());

            $dumped = $dumper->dump(array(
                'class'      => 'TubePressServiceContainer',
                'base_class' => 'ehough_iconic_ContainerBuilder'
            ));

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->warn('Caught exception trying to dump IOC container: ' . $e->getMessage());
            }

            return;
        }

        $path    = $this->_calculateCacheFilePath('service-container', 'cached-service-container.txt');
        $written = file_put_contents($path, $dumped);

        if ($written === false) {

            if ($this->_shouldLog) {

                $this->_logger->warn(sprintf('Cannot serialize IOC container to %s since it is not a writable file', $path));
            }

            return;
        }

        if ($this->_shouldLog) {

            $this->_logger->warn(sprintf('Successfully cached IOC container to %s', $path));
        }
    }

    private function _11_bootAddons()
    {
        $index       = 1;
        $count       = count($this->_addons);
        $addonLoader = tubepress_impl_patterns_sl_ServiceLocator::getAddonLoader();

        /**
         * Load add-ons.
         */

        /**
         * @var $addon tubepress_spi_addon_Addon
         */
        foreach ($this->_addons as $addon) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Attempting to boot add-on %d of %d: %s',
                    $index, $count, $addon->getName()));
            }

            $errors = $addonLoader->load($addon);

            if (count($errors) > 0) {

                foreach ($errors as $error) {

                    $this->_logger->warn($error);
                }
            }

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Done attempting to boot add-on %d of %d: %s',
                    $index, $count, $addon->getName()));
            }

            $index++;
        }
    }

    private function _12_dispatchBootCompleteEvent()
    {
        /**
         * Notify that we have loaded all plugins.
         */
        $eventDispatcher   = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $eventDispatcher->dispatch(tubepress_api_const_event_EventNames::BOOT_COMPLETE);
    }

    private function _13_flushLogIfNeeded()
    {
        $context          = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $hrps             = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $loggingEnabled   = $context->get(tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $loggingRequested = $hrps->hasParam('tubepress_debug') && $hrps->getParamValue('tubepress_debug') === true;
        $status           = $loggingEnabled && $loggingRequested;

        $this->_loggingHandler->setStatus($status);
        $this->_shouldLog = $status;
    }

    private function _calculateCacheFilePath($element, $fileName)
    {
        $dir = null;

        if (isset($this->_bootConfig['cache'][$element]['dir'])) {

            $candidate = $this->_bootConfig['cache'][$element]['dir'];

            if ($candidate && is_dir($candidate) && is_writable($candidate)) {

                $dir = $candidate;
            }
        }

        if (!$dir) {

            $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'tubepress-boot-cache-' . $this->_getInstallationUniqueKey();
        }

        if (!is_dir($dir)) {

            @mkdir($dir, 0755, true);
        }

        return $dir . DIRECTORY_SEPARATOR . $fileName;
    }

    private function _getInstallationUniqueKey()
    {
        return md5(realpath(dirname(__FILE__)));
    }
}
