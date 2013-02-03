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
     * @var bool
     */
    private static $_alreadyBooted = false;

    /**
     * @var ehough_epilog_api_ILogger
     */
    private $_logger;

    /**
     * @var ehough_epilog_api_IHandler
     */
    private $_loggingHandler;

    /**
     * @var bool Convenience variable for just this class.
     */
    private $_shouldLog = true;

    /**
     * @var ehough_pulsar_SymfonyUniversalClassLoader The classloader.
     */
    private $_classLoader;

    private $_iocContainer = null;

    /**
     * Performs TubePress-wide initialization.
     *
     * @var ehough_pulsar_SymfonyUniversalClassLoader $classLoader The TubePress classloader.
     *
     * @return null
     */
    public final function boot(ehough_pulsar_SymfonyUniversalClassLoader $classLoader)
    {
        /* don't boot twice! */
        if (self::$_alreadyBooted) {

            return;
        }

        $this->_classLoader = $classLoader;

        /*
         * Setup basic logging facilities.
         */
        $this->_loggingSetupPhaseOne();

        try {

            $this->_doBoot();

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->debug('Caught exception while booting: '.  $e->getMessage());

                //flush out log statements
                $this->_loggingHandler->setStatus(true);
            }
        }
    }

    /**
     * This is here strictly for testing :/
     *
     * @param ehough_iconic_api_IContainer $iocContainer The IoC container.
     */
    public final function setIocContainer(ehough_iconic_api_IContainer $iocContainer)
    {
        $this->_iocContainer = $iocContainer;
    }

    private function _doBoot()
    {
        /**
         * Keep track of how long this takes.
         */
        $then = microtime(true);

        if ($this->_iocContainer) {

            $coreIocContainer = $this->_iocContainer;

        } else {

            $coreIocContainer = new tubepress_impl_patterns_ioc_CoreIocContainer();
        }

        tubepress_impl_patterns_sl_ServiceLocator::setIocContainer($coreIocContainer);

        $envDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        /* WordPress likes to keep control of the output */
        if ($envDetector->isWordPress()) {

            ob_start();
        }

        if ($this->_shouldLog) {

            $this->_logger->debug('Booting!');
        }

        $pluginDiscoverer = tubepress_impl_patterns_sl_ServiceLocator::getPluginDiscoverer();
        $pluginLoader     = tubepress_impl_patterns_sl_ServiceLocator::getPluginRegistry();

        /* load plugins */
        $systemPlugins = $this->_findSystemPlugins($pluginDiscoverer);
        $userPlugins   = $this->_findUserPlugins($pluginDiscoverer);
        $allPlugins    = array_merge($systemPlugins, $userPlugins);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Found %d plugins (%d system and %d user)',
                count($allPlugins), count($systemPlugins), count($userPlugins)));

            $this->_logger->debug('Now register plugin classloaders');
        }

        /**
         * Load classpaths.
         */
        $this->_registerPluginClasspaths($allPlugins);

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering plugin classloaders. Now registering plugin IoC container extensions.');
        }

        /**
         * Load IOC container extensions.
         */
        $this->_registerIocContainerExtensions($allPlugins, $coreIocContainer);

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering plugin IoC container extensions. Now registering plugin IoC compiler passes.');
        }

        /*
         * Load IOC compiler passes.
         */
        $this->_registerIocCompilerPasses($allPlugins, $coreIocContainer);

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering plugin IoC compiler passes. Now compiling IoC container.');
        }

        /**
         * Compile all our services.
         */
        $coreIocContainer->compile();

        /**
         * Now that we have a storage manager, let's enable or disable logging permanently.
         */
        $this->_loggingSetupPhaseTwo();

        if ($this->_shouldLog) {

            $this->_logger->debug('Done compiling IoC container. Now loading plugins.');
        }

        $index = 1;
        $count = count($allPlugins);

        /**
         * Load plugins.
         */
        foreach ($allPlugins as $plugin) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Attempting to load plugin %d of %d: %s',
                    $index, $count, $plugin->getName()));
            }

            $pluginLoader->load($plugin);

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Done attempting to load plugin %d of %d: %s',
                    $index, $count, $plugin->getName()));
            }

            $index++;
        }

        if ($this->_shouldLog) {

            $now = microtime(true);

            $this->_logger->debug(sprintf('Boot completed in %f milliseconds',
                (($now - $then) * 1000.0)));
        }

        /* remember that we booted. */
        self::$_alreadyBooted = true;
    }

     private function _registerIocCompilerPasses($plugins, $coreIocContainer)
     {
         $index = 1;
         $count = count($plugins);

         foreach ($plugins as $plugin) {

             $compilerPasses = $plugin->getIocContainerCompilerPasses();

             if (count($compilerPasses) === 0) {

                 if ($this->_shouldLog) {

                     $this->_logger->debug(sprintf('(Plugin %d of %d: %s) Did not register any IoC compiler passes',
                         $index, $count, $plugin->getName()));
                 }

                 $index++;

                 continue;
             }

             foreach ($compilerPasses as $compilerPass) {

                 if ($this->_shouldLog) {

                     $this->_logger->debug(sprintf('(Plugin %d of %d: %s) Will attempt to load %s as an IoC compiler pass',
                         $index, $count, $plugin->getName(), $compilerPass));
                 }

                 try {

                     $ref = new ReflectionClass($compilerPass);

                     /** @noinspection PhpParamsInspection */
                     $coreIocContainer->addCompilerPass($ref->newInstance());

                     if ($this->_shouldLog) {

                         $this->_logger->debug(sprintf('(Plugin %d of %d: %s) Successfully loaded %s as an IoC compiler pass',
                             $index, $count, $plugin->getName(), $compilerPass));
                     }

                 } catch (Exception $e) {

                     if ($this->_shouldLog) {

                         $this->_logger->warn(sprintf('(Plugin %d of %d: %s) Failed to load %s as an IoC compiler pass: %s',
                             $index, $count, $plugin->getName(), $compilerPass, $e->getMessage()));
                     }
                 }
             }

             $index++;
         }
     }

    private function _registerIocContainerExtensions($plugins, $coreIocContainer)
    {
        $index = 1;
        $count = count($plugins);

        foreach ($plugins as $plugin) {

            $extensions = $plugin->getIocContainerExtensions();

            if (count($extensions) === 0) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Plugin %d of %d: %s) Did not register any IoC container extensions',
                        $index, $count, $plugin->getName()));
                }

                $index++;

                continue;
            }

            foreach ($extensions as $extension) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Plugin %d of %d: %s) Will attempt to load %s as an IoC container extension',
                        $index, $count, $plugin->getName(), $extension));
                }

                try {

                    $ref = new ReflectionClass($extension);

                    /** @noinspection PhpParamsInspection */
                    $coreIocContainer->registerExtension($ref->newInstance());

                    if ($this->_shouldLog) {

                        $this->_logger->debug(sprintf('(Plugin %d of %d: %s) Successfully loaded %s as an IoC container extension',
                            $index, $count, $plugin->getName(), $extension));
                    }

                } catch (Exception $e) {

                    if ($this->_shouldLog) {

                        $this->_logger->warn(sprintf('(Plugin %d of %d: %s) Failed to load %s as an IoC container extension: %s',
                            $index, $count, $plugin->getName(), $extension, $e->getMessage()));
                    }
                }
            }

            $index++;
        }
    }

    private function _registerPluginClasspaths(array $plugins)
    {
        $index = 1;
        $count = count($plugins);

        foreach ($plugins as $plugin) {

            $classPaths = $plugin->getPsr0ClassPathRoots();

            if (count($classPaths) === 0) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Plugin %d of %d: %s) Did not define any classpaths',
                        $index, $count, $plugin->getName()));
                }

                $index++;

                continue;
            }

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('(Plugin %d of %d: %s) Creating classloader that has %d classpath(s)',
                    $index, $count, $plugin->getName(), count($classPaths)));
            }

            foreach ($classPaths as $prefix => $classPath) {

                $realDir = $plugin->getAbsolutePathOfDirectory() . DIRECTORY_SEPARATOR . $classPath;

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Plugin %d of %d: %s) Registering %s as a classpath',
                        $index, $count, $plugin->getName(), $realDir));
                }

                if ($prefix) {

                    $this->_classLoader->registerDirectory($prefix, $realDir);

                } else {

                    $this->_classLoader->registerFallbackDirectory($realDir);
                }
            }

            $index++;
        }
    }

    private function _findUserPlugins(tubepress_spi_plugin_PluginDiscoverer $discoverer)
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();

        $userContentDir = $environmentDetector->getUserContentDirectory();
        $userPluginsDir = $userContentDir . '/plugins';

        return $this->_findPluginsInDirectory($userPluginsDir,
            $discoverer, true);
    }

    private function _findSystemPlugins(tubepress_spi_plugin_PluginDiscoverer $discoverer)
    {
        $corePlugins = $this->_findPluginsInDirectory(TUBEPRESS_ROOT . '/src/main/php/plugins',
            $discoverer, true);

        usort($corePlugins, array($this, '_corePluginSorter'));

        return $corePlugins;
    }

    private function _findPluginsInDirectory($directory,
        tubepress_spi_plugin_PluginDiscoverer $discoverer,
        $recursive)
    {
        if ($recursive) {

            $plugins = $discoverer->findPluginsRecursivelyInDirectory(realpath($directory));

        } else {

            $plugins = $discoverer->findPluginsNonRecursivelyInDirectory(realpath($directory));
        }

        return $plugins;
    }

    private function _loggingSetupPhaseOne()
    {
        /*
         * All loggers will share this handler. This lets us control it nicely.
         */
        $loggingHandler   = new tubepress_impl_log_TubePressLoggingHandler();
        $loggingRequested = isset($_GET['tubepress_debug']) && strcasecmp($_GET['tubepress_debug'], 'true') === 0;

        if ($loggingRequested) {

            $loggingHandler->setLevel(ehough_epilog_api_ILogger::DEBUG);

        } else {

            $loggingHandler->setLevel(ehough_epilog_api_ILogger::WARNING);
        }

        $this->_shouldLog = $loggingRequested;

        ehough_epilog_api_LoggerFactory::setHandlerStack(array($loggingHandler));

        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('TubePress Bootstrapper');

        $this->_loggingHandler = $loggingHandler;
    }

    private function _loggingSetupPhaseTwo()
    {
        $context          = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $hrps             = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $loggingEnabled   = $context->get(tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $loggingRequested = $hrps->hasParam('tubepress_debug') && strcasecmp($hrps->getParamValue('tubepress_debug'), 'true') === 0;
        $status           = $loggingEnabled && $loggingRequested;

        $this->_loggingHandler->setStatus($status);
        $this->_shouldLog = $status;
    }

    private function _corePluginSorter(tubepress_spi_plugin_Plugin $first, tubepress_spi_plugin_Plugin $second)
    {
        $firstName  = $first->getName();
        $secondName = $second->getName();

        /*
         * The core plugin always gets loaded first.
         */

        if ($firstName === 'TubePress Core') {

            return -1;
        }

        if ($secondName === 'TubePress Core') {

            return 1;
        }

        /*
         * YouTube is more popular than Vimeo, so let's load them in that order.
         */

        if (strpos($firstName, 'YouTube') !== false && strpos($secondName, 'Vimeo') !== false) {

            return -1;
        }

        if (strpos($firstName, 'Vimeo') !== false && strpos($secondName, 'YouTube') !== false) {

            return 1;
        }

        return strcasecmp($firstName, $secondName);
    }
}
