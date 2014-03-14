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
 * Handles constructing/initializing the IOC container.
 */
class tubepress_impl_boot_secondary_IocCompiler implements tubepress_spi_boot_secondary_IocCompilerInterface
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog = false;

    /**
     * @var tubepress_spi_bc_LegacyExtensionConverterInterface
     */
    private $_legacyExtensionConverter;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('IOC Container Compiler');
    }

    /**
     * Compiles the container, if necessary.
     *
     * @param tubepress_impl_ioc_IconicContainerBuilder $container
     * @param array                                     $addons
     *
     * @return void
     */
    public function compile(tubepress_impl_ioc_IconicContainerBuilder $container, array $addons)
    {
        $this->_shouldLog = $this->_logger->isHandling(ehough_epilog_Logger::DEBUG);

        $this->_compile($container, $addons);
    }

    private function _compile(tubepress_impl_ioc_IconicContainerBuilder $container, array $addons)
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

    private function _registerIocContainerExtensions(tubepress_impl_ioc_IconicContainerBuilder $containerBuilder, array $addons)
    {
        $index = 1;
        $count = count($addons);

        /**
         * @var $addon tubepress_spi_addon_AddonInterface
         */
        foreach ($addons as $addon) {

            $extensionClassNames = $addon->getIocContainerExtensions();

            if (count($extensionClassNames) === 0) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Did not register any IoC container extensions',
                        $index, $count, $addon->getName()));
                }

                $index++;

                continue;
            }

            if (!isset($this->_legacyExtensionConverter)) {

                $this->_legacyExtensionConverter = new tubepress_impl_bc_LegacyExtensionConverter();
            }

            foreach ($extensionClassNames as $extensionClassName) {

                if ($this->_shouldLog) {

                    $this->_logger->debug(sprintf('(Add-on %d of %d: %s) Will attempt to load %s as an IoC container extension',
                        $index, $count, $addon->getName(), $extensionClassName));
                }

                if ($this->_legacyExtensionConverter->isLegacyAddon($addon)) {

                    if ($this->_shouldLog) {

                        $this->_logger->debug(sprintf('(Add-on %d of %d: %s) %s appears to be a legacy extension. Attempting workaround.',
                            $index, $count, $addon->getName(), $extensionClassName));
                    }

                    $this->_registerLegacyExtension($containerBuilder, $extensionClassName, $index, $count, $addon);

                } else {

                    $this->_registerModernExtension($containerBuilder, $extensionClassName, $index, $count, $addon);
                }
            }

            $index++;
        }
    }

    private function _registerIocCompilerPasses(tubepress_impl_ioc_IconicContainerBuilder $container, array $addons)
    {
        $index = 1;
        $count = count($addons);

        /**
         * @var $addon tubepress_spi_addon_AddonInterface
         */
        foreach ($addons as $addon) {

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

                    /** @noinspection PhpParamsInspection */
                    $container->addCompilerPass($ref->newInstance());

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

    /**
     * @param tubepress_impl_ioc_IconicContainerBuilder $container
     * @param                                           $extensionClassName
     * @param                                           $index
     * @param                                           $count
     * @param                                           $addon
     */
    private function _registerModernExtension(tubepress_impl_ioc_IconicContainerBuilder $container, $extensionClassName, $index, $count, tubepress_spi_addon_AddonInterface $addon)
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

                $this->_logger->warn(sprintf('(Add-on %d of %d: %s) Failed to load %s as an IoC container extension: %s', $index, $count, $addon->getName(), $extensionClassName, $e->getMessage()));
            }
        }
    }

    private function _registerLegacyExtension(tubepress_impl_ioc_IconicContainerBuilder $container, $extensionClassName, $index, $count, tubepress_spi_addon_AddonInterface $addon)
    {
        $success = $this->_legacyExtensionConverter->evaluateLegacyExtensionClass(

            $this->_shouldLog,
            $this->_logger,
            $index,
            $count,
            $addon,
            $extensionClassName
        );

        if ($success) {

            $this->_registerModernExtension($container, $extensionClassName, $index, $count, $addon);
        }
    }

    /**
     * This is here for testing only!
     *
     * @param tubepress_spi_bc_LegacyExtensionConverterInterface $lesi
     */
    public function ___setLegacyExtensionConverter(tubepress_spi_bc_LegacyExtensionConverterInterface $lesi)
    {
        $this->_legacyExtensionConverter = $lesi;
    }
}