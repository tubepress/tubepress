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
 * Handles constructing/initializing the IOC container.
 */
class tubepress_platform_impl_boot_helper_uncached_Compiler
{
    /**
     * @var tubepress_platform_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog = false;

    public function __construct(tubepress_platform_api_log_LoggerInterface $logger)
    {
        $this->_logger    = $logger;
        $this->_shouldLog = $logger->isEnabled();
    }

    /**
     * Compiles the container, if necessary.
     *
     * @param tubepress_platform_impl_ioc_ContainerBuilder $container
     * @param array                                     $addons
     *
     * @return void
     */
    public function compile(tubepress_platform_impl_ioc_ContainerBuilder $container, array $addons)
    {
        if ($this->_shouldLog) {

            $this->_logger->debug('Compiling IOC container.');
        }

        /**
         * Load IOC container extensions.
         */
        $this->_registerIocContainerExtensions($container, $addons);

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering add-on IoC container extensions. Now registering add-on IoC compiler passes.');
        }

        /*
         * Load IOC compiler passes.
         */
        $this->_registerIocCompilerPasses($container, $addons);

        if ($this->_shouldLog) {

            $this->_logger->debug('Done registering add-on IoC compiler passes. Now compiling IoC container.');
        }

        /**
         * Compile all our services.
         */
        $container->compile();

        if ($this->_shouldLog) {

            $this->_logger->debug('Done compiling IoC container.');
        }
    }

    private function _registerIocContainerExtensions(tubepress_platform_impl_ioc_ContainerBuilder $containerBuilder, array $addons)
    {
        $index = 1;
        $count = count($addons);

        /**
         * @var $addon tubepress_platform_api_addon_AddonInterface
         */
        foreach ($addons as $addon) {

            $extensionClassNames = $addon->getExtensionClassNames();

            if (count($extensionClassNames) === 0) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Did not register any IoC container extensions',
                        $index, $count, $addon->getName()));
                }

                $index++;

                continue;
            }

            foreach ($extensionClassNames as $extensionClassName) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Will attempt to load %s as an IoC container extension',
                        $index, $count, $addon->getName(), $extensionClassName));
                }

                $this->_registerModernExtension($containerBuilder, $extensionClassName, $index, $count, $addon);
            }

            $index++;
        }
    }

    private function _registerIocCompilerPasses(tubepress_platform_impl_ioc_ContainerBuilder $container, array $addons)
    {
        $index             = 1;
        $count             = count($addons);
        $compilerPassArray = array();

        /**
         * @var $addon tubepress_platform_api_addon_AddonInterface
         */
        foreach ($addons as $addon) {

            $compilerPasses = $addon->getMapOfCompilerPassClassNamesToPriorities();

            if (count($compilerPasses) === 0) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Did not register any IoC compiler passes',
                        $index, $count, $addon->getName()));
                }

                $index++;

                continue;
            }

            $compilerPassArray = array_merge($compilerPassArray, $compilerPasses);
        }

        arsort($compilerPassArray, SORT_NUMERIC);

        foreach (array_keys($compilerPassArray) as $compilerPass) {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Will attempt to load %s as an IoC compiler pass',
                    $compilerPass));
            }

            try {

                $ref = new ReflectionClass($compilerPass);

                /** @noinspection PhpParamsInspection */
                $container->addCompilerPass($ref->newInstance());

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('Successfully loaded %s as an IoC compiler pass',
                        $compilerPass));
                }

            } catch (Exception $e) {

                if ($this->_shouldLog) {

                    $this->_logger->error(sprintf('Failed to load %s as an IoC compiler pass: %s',
                        $compilerPass, $e->getMessage()));
                }
            }
        }
    }

    /**
     * @param tubepress_platform_impl_ioc_ContainerBuilder $container
     * @param                                           $extensionClassName
     * @param                                           $index
     * @param                                           $count
     * @param                                           $addon
     */
    private function _registerModernExtension(tubepress_platform_impl_ioc_ContainerBuilder $container, $extensionClassName, $index, $count, tubepress_platform_api_addon_AddonInterface $addon)
    {
        try {

            $ref = new ReflectionClass($extensionClassName);

            /** @noinspection PhpParamsInspection */
            $container->registerExtension($ref->newInstance());

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Successfully loaded %s as an IoC container extension', $index, $count, $addon->getName(), $extensionClassName));
            }

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->error(sprintf('(Add-on %d of %d: %s) Failed to load %s as an IoC container extension: %s', $index, $count, $addon->getName(), $extensionClassName, $e->getMessage()));
            }
        }
    }
}