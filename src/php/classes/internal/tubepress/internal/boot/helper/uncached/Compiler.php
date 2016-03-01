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

/**
 * Handles constructing/initializing the IOC container.
 */
class tubepress_internal_boot_helper_uncached_Compiler
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var bool
     */
    private $_shouldLog = false;

    public function __construct(tubepress_api_log_LoggerInterface $logger)
    {
        $this->_logger    = $logger;
        $this->_shouldLog = $logger->isEnabled();
    }

    /**
     * Compiles the container, if necessary.
     *
     * @param tubepress_internal_ioc_ContainerBuilder $container
     * @param array                                     $addons
     *
     * @return void
     */
    public function compile(tubepress_internal_ioc_ContainerBuilder $container, array $addons)
    {
        if ($this->_shouldLog) {

            $this->_logDebug('Compiling service container.');
        }

        /**
         * Load container extensions.
         */
        $this->_registerIocContainerExtensions($container, $addons);

        if ($this->_shouldLog) {

            $this->_logDebug('Done registering add-on service container extensions. Now registering compiler passes.');
        }

        /*
         * Load compiler passes.
         */
        $this->_registerIocCompilerPasses($container, $addons);

        if ($this->_shouldLog) {

            $this->_logDebug('Done registering compiler passes. Now compiling container.');
        }

        /**
         * Compile all our services.
         */
        $container->compile();

        if ($this->_shouldLog) {

            $this->_logDebug('Done compiling service container.');
        }
    }

    private function _registerIocContainerExtensions(tubepress_internal_ioc_ContainerBuilder $containerBuilder, array $addons)
    {
        $index = 1;
        $count = count($addons);

        /**
         * @var $addon tubepress_api_contrib_AddonInterface
         */
        foreach ($addons as $addon) {

            $extensionClassNames = $addon->getExtensionClassNames();

            if (count($extensionClassNames) === 0) {

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('(Add-on <code>%d</code> of <code>%d</code>: <code>%s</code>) Did not register any container extensions',
                        $index, $count, $addon->getName()));
                }

                $index++;

                continue;
            }

            foreach ($extensionClassNames as $extensionClassName) {

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('(Add-on <code>%d</code> of <code>%d</code>: <code>%s</code>) Will attempt to load <code>%s</code> as a container extension',
                        $index, $count, $addon->getName(), $extensionClassName));
                }

                if ($this->_isLegacyExtension($extensionClassName)) {

                    $this->_registerLegacyExtension($containerBuilder, $extensionClassName, $index, $count, $addon);

                } else {

                    $this->_registerModernExtension($containerBuilder, $extensionClassName, $index, $count, $addon);
                }
            }

            $index++;
        }
    }

    private function _registerIocCompilerPasses(tubepress_internal_ioc_ContainerBuilder $container, array $addons)
    {
        $count             = count($addons);
        $compilerPassArray = array();

        /**
         * @var $addon tubepress_api_contrib_AddonInterface
         */
        for ($index = 1; $index <= $count; $index++) {

            $addon          = $addons[($index - 1)];
            $compilerPasses = $addon->getMapOfCompilerPassClassNamesToPriorities();

            if (count($compilerPasses) === 0) {

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('(Add-on <code>%d</code> of <code>%d</code>: <code>%s</code>) Did not register any compiler passes',
                        $index, $count, $addon->getName()));
                }

                continue;
            }

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('(Add-on <code>%d</code> of <code>%d</code>: <code>%s</code>) <code>%d</code> compiler pass(es) registered:',
                    $index, $count, $addon->getName(), count($compilerPasses)));

                foreach ($compilerPasses as $class => $priority) {

                    $this->_logDebug(sprintf('&nbsp;&nbsp;&nbsp;<code>%s</code> at priority <code>%s</code>', $class, $priority));
                }
            }

            $compilerPassArray = array_merge($compilerPassArray, $compilerPasses);
        }

        arsort($compilerPassArray, SORT_NUMERIC);

        if ($this->_shouldLog) {

            $this->_logDebug('Final compiler pass order:');

            foreach ($compilerPassArray as $class => $priority) {

                $this->_logDebug(sprintf('&nbsp;&nbsp;&nbsp;<code>%s</code> at priority <code>%s</code>', $class, $priority));
            }
        }

        foreach (array_keys($compilerPassArray) as $compilerPass) {

            try {

                $ref = new ReflectionClass($compilerPass);

                /** @noinspection PhpParamsInspection */
                $container->addCompilerPass($ref->newInstance());

                if ($this->_shouldLog) {

                    $this->_logDebug(sprintf('Successfully loaded <code>%s</code> as a compiler pass',
                        $compilerPass));
                }

            } catch (Exception $e) {

                if ($this->_shouldLog) {

                    $this->_logger->error(sprintf('Failed to load <code>%s</code> as a compiler pass: <code>%s</code>',
                        $compilerPass, $e->getMessage()));
                }
            }
        }
    }

    /**
     * @param tubepress_internal_ioc_ContainerBuilder $container
     * @param                                           $extensionClassName
     * @param                                           $index
     * @param                                           $count
     * @param                                           $addon
     */
    private function _registerModernExtension(tubepress_internal_ioc_ContainerBuilder $container, $extensionClassName, $index, $count, tubepress_api_contrib_AddonInterface $addon)
    {
        try {

            $ref = new ReflectionClass($extensionClassName);

            /** @noinspection PhpParamsInspection */
            $container->registerExtension($ref->newInstance());

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('(Add-on <code>%d</code> of <code>%d</code>: <code>%s</code>) Successfully loaded <code>%s</code> as a container extension', $index, $count, $addon->getName(), $extensionClassName));
            }

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->error(sprintf('(Add-on <code>%d</code> of <code>%d</code>: <code>%s</code>) Failed to load <code>%s</code> as a container extension: <code>%s</code>', $index, $count, $addon->getName(), $extensionClassName, $e->getMessage()));
            }
        }
    }

    private function _isLegacyExtension($extensionClassName)
    {
        try {

            $ref            = new ReflectionClass($extensionClassName);
            $interfaceNames = $ref->getInterfaceNames();
            $isLegacy       = in_array('tubepress_platform_api_ioc_ContainerExtensionInterface', $interfaceNames);

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('<code>%s</code> is a legacy extension? <code>%s</code>', $extensionClassName, $isLegacy ? 'yes' : 'no'));
            }

            return $isLegacy;

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->error(sprintf('Failed to inspect <code>%s</code>: <code>%s</code>', $extensionClassName, $e->getMessage()));
            }

            return false;
        }
    }

    private function _registerLegacyExtension(tubepress_internal_ioc_ContainerBuilder $container, $extensionClassName,
                                             $index, $count, tubepress_api_contrib_AddonInterface $addon)
    {
        if ($this->_shouldLog) {

            $this->_logDebug(sprintf('Converting <code>%s</code>', $extensionClassName));
        }

        try {
            $ref          = new ReflectionClass($extensionClassName);
            $path         = realpath($ref->getFileName());
            $fileContents = file_get_contents($path);

            if ($fileContents === false) {

                if ($this->_shouldLog) {

                    $this->_logger->error(sprintf('Failed to read <code>%s</code> for <code>%s</code>', $path, $extensionClassName));
                }

                return;
            }

            $search = array(
                'tubepress_platform_api_ioc_ContainerBuilderInterface',
                'tubepress_platform_api_ioc_ContainerExtensionInterface',
                'tubepress_platform_api_ioc_DefinitionInterface',
                'tubepress_platform_api_ioc_Reference',
                'tubepress_app_api_options_ReferenceInterface',
                'tubepress_app_api_options_ui_FieldInterface',
                'tubepress_app_api_options_ui_FieldProviderInterface',
                'tubepress_lib_api_ioc_ServiceTags',
                'tubepress_app_api_listeners_options_RegexValidatingListener',
                'tubepress_app_api_options_ui_FieldBuilderInterface',
                'tubepress_app_api_event_Events',
                'tubepress_app_api_embedded_EmbeddedProviderInterface',
                'tubepress_lib_api_template_TemplatingInterface',
                'tubepress_app_api_options_ContextInterface',
                'tubepress_platform_api_url_UrlFactoryInterface',
                'tubepress_platform_api_util_StringUtilsInterface',
            );

            $replace = array(
                'tubepress_api_ioc_ContainerBuilderInterface',
                'tubepress_spi_ioc_ContainerExtensionInterface',
                'tubepress_api_ioc_DefinitionInterface',
                'tubepress_api_ioc_Reference',
                'tubepress_api_options_ReferenceInterface',
                'tubepress_api_options_ui_FieldInterface',
                'tubepress_spi_options_ui_FieldProviderInterface',
                'tubepress_api_ioc_ServiceTags',
                'tubepress_api_options_listeners_RegexValidatingListener',
                'tubepress_api_options_ui_FieldBuilderInterface',
                'tubepress_api_event_Events',
                'tubepress_spi_embedded_EmbeddedProviderInterface',
                'tubepress_api_template_TemplatingInterface',
                'tubepress_api_options_ContextInterface',
                'tubepress_api_url_UrlFactoryInterface',
                'tubepress_api_util_StringUtilsInterface',
            );

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Successfully read <code>%s</code> for <code>%s</code>. Now converting.', $path, $extensionClassName));
            }

            $fileContents = str_replace('<?php', '', $fileContents);
            $fileContents = preg_replace('/class\s+([^\s]+)\s+implements\s+tubepress_platform_api_ioc_ContainerExtensionInterface\s+/',
                'class ${1}__converted implements tubepress_platform_api_ioc_ContainerExtensionInterface ', $fileContents);

            $newContents = str_replace($search, $replace, $fileContents);

            $evalResult = @eval($newContents);

            if ($this->_shouldLog) {

                $this->_logDebug(sprintf('Successfully loaded converted class? <code>%s</code>', $evalResult === null ? 'yes' : 'no'));
            }

            $this->_registerModernExtension($container, $extensionClassName . '__converted', $index, $count, $addon);

        } catch (Exception $e) {

            if ($this->_shouldLog) {

                $this->_logger->error(sprintf('Failed to convert <code>%s</code>: <code>%s</code>', $extensionClassName, $e->getMessage()));
            }

            return false;
        }
    }

    private function _logDebug($msg)
    {
        $this->_logger->debug(sprintf('(Compiler) %s', $msg));
    }
}