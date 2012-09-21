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
class tubepress_impl_bootstrap_TubePressBootstrapper implements tubepress_spi_bootstrap_Bootstrapper
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
        $envDetector = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();

        /* WordPress likes to keep control of the output */
        if ($envDetector->isWordPress()) {

            ob_start();
        }

        if ($this->_logger->isDebugEnabled()) {

            $this->_logger->debug('Booting!');
        }

        $pluginDiscoverer = tubepress_impl_patterns_ioc_KernelServiceLocator::getPluginDiscoverer();
        $pluginLoader     = tubepress_impl_patterns_ioc_KernelServiceLocator::getPluginRegistry();

        /* load plugins */
        $this->loadSystemPlugins($pluginDiscoverer, $pluginLoader);
        $this->loadUserPlugins($pluginDiscoverer, $pluginLoader);

        $pm = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        /* tell everyone we're booting */
        $pm->dispatchWithoutEventInstance(tubepress_api_const_event_CoreEventNames::BOOT);

        /* remember that we booted. */
        self::$_alreadyBooted = true;
    }

    private function loadUserPlugins(tubepress_spi_plugin_PluginDiscoverer $discoverer,
                                     tubepress_spi_plugin_PluginRegistry $registry)
    {
        $environmentDetector = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();

        $userContentDir = $environmentDetector->getUserContentDirectory();
        $userPluginsDir = $userContentDir . '/plugins';

        $this->loadPluginsFromDirectory($userPluginsDir,
            $discoverer, $registry);
    }

    private function loadSystemPlugins(tubepress_spi_plugin_PluginDiscoverer $discoverer,
                                       tubepress_spi_plugin_PluginRegistry $registry)
    {
        $this->loadPluginsFromDirectory(__DIR__ . '/../../../../plugins/tubepress/plugins/core',
            $discoverer, $registry);

        $this->loadPluginsFromDirectory(__DIR__ . '/../../../../plugins/tubepress/plugins/wordpresscore',
            $discoverer, $registry);
    }

    private function loadPluginsFromDirectory(
                                              $directory,
        tubepress_spi_plugin_PluginDiscoverer $discoverer,
        tubepress_spi_plugin_PluginRegistry   $registry)
    {
        $plugins = $discoverer->findPluginsNonRecursivelyInDirectory(realpath($directory));

        foreach ($plugins as $plugin) {

            $result = $registry->load($plugin);
        }
    }
}
