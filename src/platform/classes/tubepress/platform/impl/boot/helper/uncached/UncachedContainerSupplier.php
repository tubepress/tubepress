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

class tubepress_platform_impl_boot_helper_uncached_UncachedContainerSupplier
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
     * @var tubepress_platform_impl_boot_helper_uncached_Compiler
     */
    private $_iocCompiler;

    /**
     * @var tubepress_platform_impl_ioc_ContainerBuilder
     */
    private $_containerBuilder;

    /**
     * @var ehough_iconic_dumper_DumperInterface
     */
    private $_containerDumper;

    /**
     * @var tubepress_platform_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var ehough_pulsar_MapClassLoader
     */
    private $_mapClassLoader;

    /**
     * @var tubepress_platform_impl_boot_helper_uncached_contrib_ManifestFinder
     */
    private $_manifestFinder;

    /**
     * @var tubepress_platform_impl_boot_helper_uncached_contrib_AddonFactory
     */
    private $_addonFactory;

    /**
     * @var callable
     */
    private $_serializer;

    public function __construct(tubepress_platform_api_log_LoggerInterface                          $logger,
                                tubepress_platform_impl_boot_helper_uncached_contrib_ManifestFinder $manifestFinder,
                                tubepress_platform_impl_boot_helper_uncached_contrib_AddonFactory   $addonFactory,
                                tubepress_platform_impl_boot_helper_uncached_Compiler               $ici,
                                tubepress_platform_api_boot_BootSettingsInterface                   $sfri)
    {
        $this->_logger         = $logger;
        $this->_shouldLog      = $logger->isEnabled();
        $this->_iocCompiler    = $ici;
        $this->_bootSettings   = $sfri;
        $this->_manifestFinder = $manifestFinder;
        $this->_addonFactory   = $addonFactory;
    }

    public function getNewIconicContainer()
    {
        if (!isset($this->_containerBuilder)) {

            $this->_containerBuilder = new tubepress_platform_impl_ioc_ContainerBuilder();
        }

        $this->_containerBuilder->set('tubepress_platform_api_ioc_ContainerInterface',      $this->_containerBuilder);
        $this->_containerBuilder->set('ehough_iconic_ContainerInterface',                   $this->_containerBuilder->getDelegateContainerBuilder());
        $this->_containerBuilder->set('tubepress_platform_impl_log_BootLogger',             $this->_logger);
        $this->_containerBuilder->set(tubepress_platform_api_boot_BootSettingsInterface::_, $this->_bootSettings);

        $addons = $this->_findAllAddons();

        if ($this->_bootSettings->isClassLoaderEnabled()) {

            $this->_setupClassLoader($addons);
        }

        $this->_iocCompiler->compile($this->_containerBuilder, $addons);

        if ($this->_bootSettings->isClassLoaderEnabled()) {

            spl_autoload_unregister(array($this->_mapClassLoader, 'loadClass'));
        }

        return $this->_convertToIconicContainer($this->_containerBuilder);
    }

    private function _findAllAddons()
    {
        $manifests = $this->_manifestFinder->find();
        $addons    = array();

        foreach ($manifests as $path => $data) {

            try {

                $errors = $this->_addonFactory->fromManifestData($path, $data);

                if (is_array($errors)) {

                    //these will have been logged already
                    continue;
                }

                $addons[] = $errors;

            } catch (Exception $e) {

                continue;
            }
        }

        if (!isset($this->_serializer)) {

            $this->_serializer = array(
                new tubepress_platform_impl_boot_helper_uncached_Serializer($this->_bootSettings),
                'serialize'
            );
        }

        $artifacts = array(
            'add-ons' => call_user_func($this->_serializer, $addons, $this->_bootSettings)
        );

        $this->_containerBuilder->setParameter(
            tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS,
            $artifacts
        );

        return $addons;
    }

    private function _setupClassLoader(array $addons)
    {
        $addonClassMap = $this->_getClassMapFromAddons($addons);
        $fullClassMap  = require TUBEPRESS_ROOT . '/src/platform/scripts/classloading/classmap.php';
        $finalClassMap = array_merge($fullClassMap, $addonClassMap);

        $this->_mapClassLoader = new ehough_pulsar_MapClassLoader($finalClassMap);
        $this->_mapClassLoader->register();

        $existingArtifacts = $this->_containerBuilder->getParameter(tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS);

        $artifacts = array_merge($existingArtifacts, array(
            'classloading' => array(
                'map' => $finalClassMap
            )
        ));

        $this->_containerBuilder->setParameter(tubepress_platform_impl_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS,
            $artifacts);
    }

    /**
     * @param tubepress_platform_impl_ioc_ContainerBuilder $containerBuilder
     *
     * @return ehough_iconic_ContainerInterface
     */
    private function _convertToIconicContainer(tubepress_platform_impl_ioc_ContainerBuilder $containerBuilder)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Preparing to store boot to cache.');
        }

        $dumpedContainerText = $this->_getDumpedIconicContainerAsString($containerBuilder->getDelegateContainerBuilder());

        if ($this->_bootSettings->isSystemCacheEnabled()) {

            $cachePath = $this->_bootSettings->getPathToSystemCacheDirectory();

            $storagePath = sprintf('%s%sTubePress-%s-ServiceContainer.php', $cachePath, DIRECTORY_SEPARATOR, TUBEPRESS_VERSION);

        } else {

            $storagePath = tempnam(sys_get_temp_dir(), 'TubePressServiceContainer');
        }

        if (!is_dir(dirname($storagePath))) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Attempting to create all the parent directories of %s', $storagePath));
            }

            $success = @mkdir(dirname($storagePath), 0755, true);

            if ($this->_shouldLog) {

                if ($success === true) {

                    $this->_logger->debug(sprintf('Created all the parent directories of %s', $storagePath));

                } else {

                    $this->_logger->error(sprintf('Failed to create all the parent directories of %s', $storagePath));
                }
            }

            if ($success !== true) {

                return $containerBuilder->getDelegateContainerBuilder();
            }
        }

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

    private function _getClassMapFromAddons(array $addons)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Examining classloading data for %d add-ons', count($addons)));
        }

        $toReturn = array();

        /**
         * @var $addon tubepress_platform_api_addon_AddonInterface
         */
        foreach ($addons as $addon) {

            $map = $addon->getClassMap();

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Add-on %s has a classmap of size %d',
                    $addon->getName(), count($map)));
            }

            $toReturn = array_merge($toReturn, $map);
        }

        if ($this->_shouldLog) {

            $this->_logger->debug(sprintf('Done gathering classmaps for %d add-ons.', count($addons)));
        }

        return $toReturn;
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

    public function __setSerializer(array $callback)
    {
        $this->_serializer = $callback;
    }
}
