<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Performs TubePress-wide initialization.
 */
class tubepress_impl_bootstrap_TubePressBootstrapper
{
    private static $_alreadyBooted = false;

    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('TubePress Bootstrapper');
    }

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

        try {

            $this->_doBoot();

        } catch (Exception $e) {

            $this->_logger->debug('Caught exception while booting: '.  $e->getMessage());
        }
    }

    private function _doBoot()
    {
        $coreIocContainer = new tubepress_impl_patterns_ioc_CoreIocContainer();
        tubepress_impl_patterns_ioc_KernelServiceLocator::setCoreIocContainer($coreIocContainer);

        $envDetector = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();

        /* WordPress likes to keep control of the output */
        if ($envDetector->isWordPress()) {

            ob_start();
        }

        $loggerDebugEnabled = $this->_logger->isDebugEnabled();

        if ($loggerDebugEnabled) {

            $this->_logger->debug('Booting!');
        }

        $pluginDiscoverer = tubepress_impl_patterns_ioc_KernelServiceLocator::getPluginDiscoverer();
        $pluginLoader     = tubepress_impl_patterns_ioc_KernelServiceLocator::getPluginRegistry();

        /* load plugins */
        $systemPlugins = $this->_findSystemPlugins($pluginDiscoverer);
        $userPlugins   = $this->_findUserPlugins($pluginDiscoverer);
        $allPlugins    = array_merge($systemPlugins, $userPlugins);

        if ($loggerDebugEnabled) {

            $this->_logger->debug(sprintf('Found %d plugins (%d system and %d user)',
                count($allPlugins), count($systemPlugins), count($userPlugins)));
        }

        /**
         * Load classpaths.
         */
        $this->_registerPluginClasspaths($allPlugins, $loggerDebugEnabled);

        /**
         * Load IOC container extensions.
         */
        $this->_registerIocContainerExtensions($allPlugins, $coreIocContainer, $loggerDebugEnabled);

        /**
         * Compile all our services.
         */
        $coreIocContainer->compile();

        /**
         * Load plugins.
         */
        foreach ($allPlugins as $plugin) {

            $pluginLoader->load($plugin);
        }

        /* remember that we booted. */
        self::$_alreadyBooted = true;
    }

    private function _registerIocContainerExtensions($plugins, tubepress_impl_patterns_ioc_CoreIocContainer $coreIocContainer,
                                                     $loggerDebugEnabled)
    {
        foreach ($plugins as $plugin) {

            $extensions = $plugin->getIocContainerExtensions();

            if (count($extensions) === 0) {

                if ($loggerDebugEnabled) {

                    $this->_logger->debug(sprintf('Plugin %s did not register any IoC container extensions',
                        $plugin->getName()));
                }

                continue;
            }

            foreach ($extensions as $extension) {

                if ($loggerDebugEnabled) {

                    $this->_logger->debug(sprintf('Will attempt to load %s as an IoC container extension for plugin %s',
                        $extension, $plugin->getName()));
                }

                try {

                    $ref = new ReflectionClass($extension);

                    /** @noinspection PhpParamsInspection */
                    $coreIocContainer->registerExtension($ref->newInstance());

                } catch (Exception $e) {

                    $this->_logger->warn(sprintf('Failed to load %s as an IoC container extension for plugin %s: %s',
                        $extension, $plugin->getName(), $e->getMessage()));
                }
            }
        }
    }

    private function _registerPluginClasspaths(array $plugins, $loggerDebugEnabled)
    {
        foreach ($plugins as $plugin) {

            $classPaths = $plugin->getPsr0ClassPathRoots();

            if (count($classPaths) === 0) {

                if ($loggerDebugEnabled) {

                    $this->_logger->debug(sprintf('Plugin %s did not register any classloaders',
                        $plugin->getName()));
                }

                continue;
            }

            if ($loggerDebugEnabled) {

                $this->_logger->debug(sprintf('Creating classloader for %s plugin that has %d classpath(s)',
                    $plugin->getName(), count($classPaths)));
            }

            $loader = new ehough_pulsar_SymfonyUniversalClassLoader();

            foreach ($classPaths as $classPath) {

                $realDir = $plugin->getAbsolutePathOfDirectory() . DIRECTORY_SEPARATOR . $classPath;

                if ($loggerDebugEnabled) {

                    $this->_logger->debug(sprintf('Registering %s as a classpath for plugin %s',
                        $realDir, $plugin->getName()));
                }

                $loader->registerFallbackDirectory($realDir);
            }

            $loader->register();
        }
    }

    private function _findUserPlugins(tubepress_spi_plugin_PluginDiscoverer $discoverer)
    {
        $environmentDetector = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();

        $userContentDir = $environmentDetector->getUserContentDirectory();
        $userPluginsDir = $userContentDir . '/plugins';

        return $this->_findPluginsInDirectory($userPluginsDir,
            $discoverer, true);
    }

    private function _findSystemPlugins(tubepress_spi_plugin_PluginDiscoverer $discoverer)
    {
        $corePlugins = $this->_findPluginsInDirectory(TUBEPRESS_ROOT . '/src/main/php/plugins/core',
            $discoverer, false);

        $addOnPlugins = $this->_findPluginsInDirectory(TUBEPRESS_ROOT . '/src/main/php/plugins/addon',
            $discoverer, true);

        return array_merge($corePlugins, $addOnPlugins);
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
}
