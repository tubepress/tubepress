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
class tubepress_impl_boot_helper_secondary_UncachedContainerSupplier
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog;

    /**
     * @var tubepress_impl_boot_helper_secondary_ClassLoaderPrimer
     */
    private $_bootHelperClassLoadingHelper;

    /**
     * @var tubepress_impl_boot_helper_secondary_IocCompiler
     */
    private $_bootHelperIocHelper;

    /**
     * @var tubepress_api_contrib_RegistryInterface
     */
    private $_bootHelperAddonDiscoverer;

    /**
     * @var tubepress_impl_ioc_ContainerBuilder
     */
    private $_containerBuilder;

    /**
     * @var ehough_iconic_dumper_DumperInterface
     */
    private $_containerDumper;

    /**
     * @var tubepress_impl_boot_BootSettings
     */
    private $_settingsFileReader;

    /**
     * @var ehough_pulsar_ComposerClassLoader
     */
    private $_classLoader;

    public function __construct(tubepress_api_log_LoggerInterface                      $logger,
                                tubepress_impl_boot_helper_secondary_ClassLoaderPrimer $clsi,
                                tubepress_api_contrib_RegistryInterface                $adi,
                                tubepress_impl_boot_helper_secondary_IocCompiler       $ici,
                                ehough_pulsar_ComposerClassLoader                      $classLoader,
                                tubepress_impl_boot_BootSettings                       $sfri)
    {
        $this->_logger                       = $logger;
        $this->_shouldLog                    = $logger->isEnabled();
        $this->_bootHelperClassLoadingHelper = $clsi;
        $this->_bootHelperAddonDiscoverer    = $adi;
        $this->_bootHelperIocHelper          = $ici;
        $this->_settingsFileReader           = $sfri;
        $this->_classLoader                  = $classLoader;
    }

    public function getNewIconicContainer()
    {
        $addons = $this->_bootHelperAddonDiscoverer->getAll();

        if (!isset($this->_containerBuilder)) {

            $this->_containerBuilder = new tubepress_impl_ioc_ContainerBuilder();
        }

        if ($this->_settingsFileReader->isClassLoaderEnabled()) {

            $this->_bootHelperClassLoadingHelper->addClassHintsForAddons($addons, $this->_classLoader);

            $fullClassMap = require TUBEPRESS_ROOT . '/src/platform/scripts/classmaps/full.php';

            $this->_classLoader->addToClassMap($fullClassMap);

            $classMap = $this->_classLoader->getClassMap();
            $this->_containerBuilder->setParameter('classMap', $classMap);
        }

        $this->_containerBuilder->set('tubepress_api_ioc_ContainerInterface',      $this->_containerBuilder);
        $this->_containerBuilder->set('ehough_iconic_ContainerInterface',          $this->_containerBuilder->getDelegateContainerBuilder());
        $this->_containerBuilder->set('tubepress_impl_log_BootLogger',             $this->_logger);
        $this->_containerBuilder->set(tubepress_api_boot_BootSettingsInterface::_, $this->_settingsFileReader);

        $this->_bootHelperIocHelper->compile($this->_containerBuilder, $addons);

        if ($this->_settingsFileReader->isContainerCacheEnabled()) {

            return $this->_tryToCacheAndReturnIconicContainer($this->_containerBuilder);
        }

        return $this->_containerBuilder->getDelegateContainerBuilder();
    }

    /**
     * @param tubepress_impl_ioc_ContainerBuilder $containerBuilder
     *
     * @return ehough_iconic_ContainerInterface
     */
    private function _tryToCacheAndReturnIconicContainer(tubepress_impl_ioc_ContainerBuilder $containerBuilder)
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
     * @param tubepress_impl_ioc_ContainerBuilder $containerBuilder
     */
    public function __setContainerBuilder(tubepress_impl_ioc_ContainerBuilder $containerBuilder)
    {
        $this->_containerBuilder = $containerBuilder;
    }

    public function __setContainerDumper(ehough_iconic_dumper_DumperInterface $dumper)
    {
        $this->_containerDumper = $dumper;
    }
}
