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

class tubepress_internal_boot_helper_uncached_UncachedContainerSupplier
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
     * @var tubepress_internal_boot_helper_uncached_Compiler
     */
    private $_iocCompiler;

    /**
     * @var tubepress_internal_ioc_ContainerBuilder
     */
    private $_containerBuilder;

    /**
     * @var \Symfony\Component\DependencyInjection\Dumper\DumperInterface
     */
    private $_containerDumper;

    /**
     * @var tubepress_api_boot_BootSettingsInterface
     */
    private $_bootSettings;

    /**
     * @var Symfony\Component\ClassLoader\MapClassLoader
     */
    private $_mapClassLoader;

    /**
     * @var tubepress_internal_boot_helper_uncached_contrib_ManifestFinder
     */
    private $_manifestFinder;

    /**
     * @var tubepress_internal_boot_helper_uncached_contrib_AddonFactory
     */
    private $_addonFactory;

    /**
     * @var callable
     */
    private $_serializer;

    public function __construct(tubepress_api_log_LoggerInterface                              $logger,
                                tubepress_internal_boot_helper_uncached_contrib_ManifestFinder $manifestFinder,
                                tubepress_internal_boot_helper_uncached_contrib_AddonFactory   $addonFactory,
                                tubepress_internal_boot_helper_uncached_Compiler               $ici,
                                tubepress_api_boot_BootSettingsInterface                       $sfri)
    {
        $this->_logger         = $logger;
        $this->_shouldLog      = $logger->isEnabled();
        $this->_iocCompiler    = $ici;
        $this->_bootSettings   = $sfri;
        $this->_manifestFinder = $manifestFinder;
        $this->_addonFactory   = $addonFactory;
    }

    public function getNewSymfonyContainer()
    {
        if (!isset($this->_containerBuilder)) {

            $this->_containerBuilder = new tubepress_internal_ioc_ContainerBuilder();
        }

        $this->_containerBuilder->set('tubepress_api_ioc_ContainerInterface',      $this->_containerBuilder);
        $this->_containerBuilder->set('symfony_service_container',                 $this->_containerBuilder->getDelegateContainerBuilder());
        $this->_containerBuilder->set('tubepress_internal_logger_BootLogger',      $this->_logger);
        $this->_containerBuilder->set(tubepress_api_boot_BootSettingsInterface::_, $this->_bootSettings);

        $addons = $this->_findAllAddons();

        if ($this->_bootSettings->isClassLoaderEnabled()) {

            $this->_setupClassLoader($addons);
        }

        $this->_iocCompiler->compile($this->_containerBuilder, $addons);

        if ($this->_bootSettings->isClassLoaderEnabled()) {

            spl_autoload_unregister(array($this->_mapClassLoader, 'loadClass'));
        }

        return $this->_convertToSymfonyContainer($this->_containerBuilder);
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
                new tubepress_internal_boot_helper_uncached_Serializer($this->_bootSettings),
                'serialize'
            );
        }

        $artifacts = array(
            'add-ons' => call_user_func($this->_serializer, $addons, $this->_bootSettings)
        );

        $this->_containerBuilder->setParameter(
            tubepress_internal_boot_PrimaryBootstrapper::CONTAINER_PARAM_BOOT_ARTIFACTS,
            $artifacts
        );

        return $addons;
    }

    private function _setupClassLoader(array $addons)
    {
        $addonClassMap         = $this->_getClassMapFromAddons($addons);
        $fullClassMap          = require TUBEPRESS_ROOT . '/src/php/scripts/classloading/classmap.php';
        $finalClassMap         = array_merge($fullClassMap, $addonClassMap);
        $this->_mapClassLoader = new \Symfony\Component\ClassLoader\MapClassLoader($finalClassMap);
        $systemCachePath       = $this->_bootSettings->getPathToSystemCacheDirectory();
        $dumpPath              = $systemCachePath . DIRECTORY_SEPARATOR . 'classmap.php';
        $exportedClassMap      = var_export($finalClassMap, true);
        $toDump                = sprintf('<?php return %s;', $exportedClassMap);

        if ($this->_shouldLog) {

            $this->_logDebug(
                sprintf('Our final classmap has <code>%d</code> classes in it. We\'ll try to dump it to <code>%s</code>.',
                count($finalClassMap),
                $dumpPath
            ));
        }

        $this->_mapClassLoader->register();

        $result = @file_put_contents($dumpPath, $toDump);

        if ($this->_shouldLog) {

            if ($result !== false) {

                $msg = sprintf('Successfully wrote <code>%d</code> bytes to <code>%s</code>',
                    $result,
                    $dumpPath
                );

            } else {

                $msg = sprintf('Unable to write to <code>%s</code>', $dumpPath);
            }

            $this->_logDebug($msg);
        }
    }

    /**
     * @param tubepress_internal_ioc_ContainerBuilder $containerBuilder
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface
     */
    private function _convertToSymfonyContainer(tubepress_internal_ioc_ContainerBuilder $containerBuilder)
    {
        if ($this->_shouldLog) {

            $this->_logDebug('Preparing to store boot to cache.');
        }

        $dumpedContainerText = $this->_getDumpedSymfonyContainerAsString($containerBuilder->getDelegateContainerBuilder());

        if ($this->_bootSettings->isSystemCacheEnabled()) {

            $cachePath = $this->_bootSettings->getPathToSystemCacheDirectory();

            $storagePath = sprintf('%s%sTubePressServiceContainer.php', $cachePath, DIRECTORY_SEPARATOR);

        } else {

            $storagePath = tempnam(sys_get_temp_dir(), 'TubePressServiceContainer');
        }

        if (!is_dir(dirname($storagePath))) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Attempting to create all the parent directories of <code>%s</code>', $storagePath));
            }

            $success = @mkdir(dirname($storagePath), 0755, true);

            if ($this->_shouldLog) {

                if ($success === true) {

                    $this->_logDebug(sprintf('Created all the parent directories of <code>%s</code>', $storagePath));

                } else {

                    $this->_logger->error(sprintf('Failed to create all the parent directories of <code>%s</code>', $storagePath));
                }
            }

            if ($success !== true) {

                return $containerBuilder->getDelegateContainerBuilder();
            }
        }

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Now writing dumped container to <code>%s</code>', $storagePath));
        }

        $success = @file_put_contents($storagePath, $dumpedContainerText) !== false;

        if ($success) {

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Saved service container to <code>%s</code>. Now including it.', $storagePath));
            }

            if (!class_exists('TubePressServiceContainer', false)) {

                /** @noinspection PhpIncludeInspection */
                require $storagePath;
            }

        } else {

            if ($this->_shouldLog) {

                $this->_logger->error(sprintf('Could not write service container to <code>%s</code>.', $storagePath));
            }

            return $containerBuilder->getDelegateContainerBuilder();
        }

        /** @noinspection PhpUndefinedClassInspection */
        return new TubePressServiceContainer();
    }

    private function _getDumpedSymfonyContainerAsString(\Symfony\Component\DependencyInjection\ContainerBuilder $containerBuilder)
    {
        if ($this->_shouldLog) {

            $this->_logDebug('Preparing to dump container builder to string');
        }

        $dumpConfig = array(

            'class' => 'TubePressServiceContainer'
        );

        if (!isset($this->_containerDumper)) {

            $this->_containerDumper = new \Symfony\Component\DependencyInjection\Dumper\PhpDumper($containerBuilder);
        }

        $dumped = $this->_containerDumper->dump($dumpConfig);

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Done dumping container builder to string. Check the HTML source to view the full' .
                ' container. <div style="display:none"><pre>%s</pre></div>', $dumped));
        }

        return $dumped;
    }

    private function _getClassMapFromAddons(array $addons)
    {
        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Examining classloading data for <code>%d</code> add-ons', count($addons)));
        }

        $toReturn = array();

        /**
         * @var $addon tubepress_api_contrib_AddonInterface
         */
        foreach ($addons as $addon) {

            $map = $addon->getClassMap();

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Add-on <code>%s</code> has a classmap of size <code>%d</code>',
                    $addon->getName(), count($map)));
            }

            $toReturn = array_merge($toReturn, $map);
        }

        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Done gathering classmaps for <code>%d</code> add-ons.', count($addons)));
        }

        return $toReturn;
    }

    /**
     * This is here strictly for testing! :/
     *
     * @param tubepress_internal_ioc_ContainerBuilder $containerBuilder
     */
    public function __setContainerBuilder(tubepress_internal_ioc_ContainerBuilder $containerBuilder)
    {
        $this->_containerBuilder = $containerBuilder;
    }

    public function __setContainerDumper(\Symfony\Component\DependencyInjection\Dumper\DumperInterface $dumper)
    {
        $this->_containerDumper = $dumper;
    }

    public function __setSerializer(array $callback)
    {
        $this->_serializer = $callback;
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Uncached Container Supplier) %s', $msg));
    }
}
