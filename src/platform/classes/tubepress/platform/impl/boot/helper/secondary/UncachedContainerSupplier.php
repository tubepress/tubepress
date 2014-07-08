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
 * Retrieves settings from a PHP file.
 */
class tubepress_platform_impl_boot_helper_secondary_UncachedContainerSupplier
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var tubepress_platform_impl_boot_helper_secondary_ClassLoaderPrimer
     */
    private $_bootHelperClassLoadingHelper;

    /**
     * @var tubepress_platform_impl_boot_helper_secondary_IocCompiler
     */
    private $_bootHelperIocHelper;

    /**
     * @var tubepress_platform_api_contrib_RegistryInterface
     */
    private $_bootHelperAddonDiscoverer;

    /**
     * @var tubepress_platform_impl_ioc_ContainerBuilder
     */
    private $_containerBuilder;

    /**
     * @var ehough_iconic_dumper_DumperInterface
     */
    private $_containerDumper;

    /**
     * @var tubepress_platform_impl_boot_BootSettings
     */
    private $_settingsFileReader;

    /**
     * @var ehough_pulsar_MapClassLoader
     */
    private $_mapClassLoader;

    private $_psr0ClassLoader;

    public function __construct(tubepress_platform_api_log_LoggerInterface                      $logger,
                                tubepress_platform_impl_boot_helper_secondary_ClassLoaderPrimer $clsi,
                                tubepress_platform_api_contrib_RegistryInterface                $adi,
                                tubepress_platform_impl_boot_helper_secondary_IocCompiler       $ici,
                                tubepress_platform_impl_boot_BootSettings                       $sfri)
    {
        $this->_logger                       = $logger;
        $this->_shouldLog                    = $logger->isEnabled();
        $this->_bootHelperClassLoadingHelper = $clsi;
        $this->_bootHelperAddonDiscoverer    = $adi;
        $this->_bootHelperIocHelper          = $ici;
        $this->_settingsFileReader           = $sfri;
    }

    public function getNewIconicContainer()
    {
        $addons = $this->_bootHelperAddonDiscoverer->getAll();

        if (!isset($this->_containerBuilder)) {

            $this->_containerBuilder = new tubepress_platform_impl_ioc_ContainerBuilder();
        }

        if ($this->_settingsFileReader->isClassLoaderEnabled()) {

            $this->_setupClassLoaderAndContainerParams($addons);
        }

        $this->_containerBuilder->set('tubepress_platform_api_ioc_ContainerInterface',      $this->_containerBuilder);
        $this->_containerBuilder->set('ehough_iconic_ContainerInterface',          $this->_containerBuilder->getDelegateContainerBuilder());
        $this->_containerBuilder->set('tubepress_platform_impl_log_BootLogger',             $this->_logger);
        $this->_containerBuilder->set(tubepress_platform_api_boot_BootSettingsInterface::_, $this->_settingsFileReader);

        $this->_bootHelperIocHelper->compile($this->_containerBuilder, $addons);

        if ($this->_settingsFileReader->isContainerCacheEnabled()) {

            $toReturn = $this->_tryToCacheAndReturnIconicContainer($this->_containerBuilder);

        } else {

            $toReturn = $this->_containerBuilder->getDelegateContainerBuilder();
        }

        if ($this->_settingsFileReader->isClassLoaderEnabled()) {

            spl_autoload_unregister(array($this->_mapClassLoader, 'loadClass'));

            if (isset($this->_psr0ClassLoader)) {

                spl_autoload_unregister(array($this->_psr0ClassLoader, 'loadClass'));
            }
        }

        return $toReturn;
    }

    private function _setupClassLoaderAndContainerParams(array $addons)
    {
        $addonClassMap = $this->_bootHelperClassLoadingHelper->getClassMapFromAddons($addons);
        $addonClassMap = array_filter($addonClassMap, array($this, '__filterAddonClassMap'));
        $psr0Roots     = $this->_bootHelperClassLoadingHelper->getPsr0Roots($addons);
        $psr0Fallbacks = $this->_bootHelperClassLoadingHelper->getPsr0Fallbacks($addons);
        $fullClassMap  = require TUBEPRESS_ROOT . '/src/platform/scripts/classloading/classmap.php';
        $finalClassMap = array_merge($fullClassMap, $addonClassMap);

        $this->_mapClassLoader = new ehough_pulsar_MapClassLoader($finalClassMap);
        $this->_mapClassLoader->register();
        $this->_containerBuilder->setParameter('classloading-classmap', $finalClassMap);

        if (!empty($psr0Fallbacks) || !empty($psr0Roots)) {

            $this->_psr0ClassLoader = new ehough_pulsar_UniversalClassLoader();
            $this->_psr0ClassLoader->registerPrefixes($psr0Roots);
            $this->_psr0ClassLoader->registerNamespaces($psr0Roots);
            $this->_psr0ClassLoader->registerPrefixFallbacks($psr0Fallbacks);
            $this->_psr0ClassLoader->registerNamespaceFallbacks($psr0Fallbacks);
            $this->_psr0ClassLoader->register();

            $this->_containerBuilder->setParameter('classloading-psr0-fallbacks', $psr0Fallbacks);
            $this->_containerBuilder->setParameter('classloading-psr0-prefixed-paths', $psr0Roots);
        }
    }

    /**
     * @param tubepress_platform_impl_ioc_ContainerBuilder $containerBuilder
     *
     * @return ehough_iconic_ContainerInterface
     */
    private function _tryToCacheAndReturnIconicContainer(tubepress_platform_impl_ioc_ContainerBuilder $containerBuilder)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Preparing to store boot to cache.');
        }

        $dumpedContainerText = $this->_getDumpedIconicContainerAsString($containerBuilder->getDelegateContainerBuilder());
        $storagePath         = $this->_settingsFileReader->getPathToContainerCacheFile();

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Now writing dumped container to %s', $storagePath));
        }

        $success = @file_put_contents($storagePath, $dumpedContainerText) !== false;

        if ($success) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Saved service container to %s. Now including it.', $storagePath));
            }

            if (!class_exists('TubePressServiceContainer', false)) {

                /** @noinspection PhpIncludeInspection */
                require $storagePath;
            }

        } else {

            if ($this->_shouldLog) {

                $this->_logger->error(sprintf('Could not write service container to %s.', $storagePath));
            }

            return $containerBuilder->getDelegateContainerBuilder();
        }

        /** @noinspection PhpUndefinedClassInspection */
        return new TubePressServiceContainer();
    }

    private function _getDumpedIconicContainerAsString(ehough_iconic_ContainerBuilder $containerBuilder)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Preparing to dump container builder to string');
        }

        $dumpConfig = array(

            'class' => 'TubePressServiceContainer'
        );

        if (!isset($this->_containerDumper)) {

            $this->_containerDumper = new ehough_iconic_dumper_PhpDumper($containerBuilder);
        }

        $dumped = $this->_containerDumper->dump($dumpConfig);

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Done dumping container builder to string. Check the HTML source to view the full' .
                ' container. <div style="display:none"><pre>%s</pre></div>', $dumped));
        }

        return $dumped;
    }


    /**
     * This is here strictly for testing! :/
     *
     * @param tubepress_platform_impl_ioc_ContainerBuilder $containerBuilder
     */
    public function __setContainerBuilder(tubepress_platform_impl_ioc_ContainerBuilder $containerBuilder)
    {
        $this->_containerBuilder = $containerBuilder;
    }

    public function __setContainerDumper(ehough_iconic_dumper_DumperInterface $dumper)
    {
        $this->_containerDumper = $dumper;
    }

    public function __filterAddonClassMap($filepath)
    {
        return true;
    }
}
