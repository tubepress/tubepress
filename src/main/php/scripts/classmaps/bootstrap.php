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

$_bootStrapClassMapEpilogPrefix = TUBEPRESS_ROOT . '/vendor/ehough/epilog/src/main/php/ehough/epilog';
$_bootStrapClassMapIconicPrefix = TUBEPRESS_ROOT . '/vendor/ehough/iconic/src/main/php/ehough/iconic';
$_bootStrapClassMapSysPrefix    = TUBEPRESS_ROOT . '/src/main/php/classes/tubepress';

return array(

    'ehough_epilog_formatter_FormatterInterface'      => $_bootStrapClassMapEpilogPrefix . '/formatter/FormatterInterface.php',
    'ehough_epilog_formatter_LineFormatter'           => $_bootStrapClassMapEpilogPrefix . '/formatter/LineFormatter.php',
    'ehough_epilog_formatter_NormalizerFormatter'     => $_bootStrapClassMapEpilogPrefix . '/formatter/NormalizerFormatter.php',
    'ehough_epilog_handler_AbstractHandler'           => $_bootStrapClassMapEpilogPrefix . '/handler/AbstractHandler.php',
    'ehough_epilog_handler_AbstractProcessingHandler' => $_bootStrapClassMapEpilogPrefix . '/handler/AbstractProcessingHandler.php',
    'ehough_epilog_handler_HandlerInterface'          => $_bootStrapClassMapEpilogPrefix . '/handler/HandlerInterface.php',
    'ehough_epilog_handler_NullHandler'               => $_bootStrapClassMapEpilogPrefix . '/handler/NullHandler.php',
    'ehough_epilog_LoggerFactory'                     => $_bootStrapClassMapEpilogPrefix . '/LoggerFactory.php',
    'ehough_epilog_Logger'                            => $_bootStrapClassMapEpilogPrefix . '/Logger.php',
    'ehough_epilog_psr_AbstractLogger'                => $_bootStrapClassMapEpilogPrefix . '/psr/AbstractLogger.php',
    'ehough_epilog_psr_InvalidArgumentException'      => $_bootStrapClassMapEpilogPrefix . '/psr/InvalidArgumentException.php',
    'ehough_epilog_psr_LoggerAwareInterface'          => $_bootStrapClassMapEpilogPrefix . '/psr/LoggerAwareInterface.php',
    'ehough_epilog_psr_LoggerInterface'               => $_bootStrapClassMapEpilogPrefix . '/psr/LoggerInterface.php',

    'ehough_iconic_Container'                           => $_bootStrapClassMapIconicPrefix . '/Container.php',
    'ehough_iconic_ContainerBuilder'                    => $_bootStrapClassMapIconicPrefix . '/ContainerBuilder.php',
    'ehough_iconic_ContainerInterface'                  => $_bootStrapClassMapIconicPrefix . '/ContainerInterface.php',
    'ehough_iconic_Definition'                          => $_bootStrapClassMapIconicPrefix . '/Definition.php',
    'ehough_iconic_dumper_Dumper'                       => $_bootStrapClassMapIconicPrefix . '/dumper/Dumper.php',
    'ehough_iconic_dumper_DumperInterface'              => $_bootStrapClassMapIconicPrefix . '/dumper/DumperInterface.php',
    'ehough_iconic_dumper_PhpDumper'                    => $_bootStrapClassMapIconicPrefix . '/dumper/PhpDumper.php',
    'ehough_iconic_exception_ExceptionInterface'        => $_bootStrapClassMapIconicPrefix . '/exception/ExceptionInterface.php',
    'ehough_iconic_exception_InvalidArgumentException'  => $_bootStrapClassMapIconicPrefix . '/exception/InvalidArgumentException.php',
    'ehough_iconic_exception_ServiceNotFoundException'  => $_bootStrapClassMapIconicPrefix . '/exception/ServiceNotFoundException.php',
    'ehough_iconic_IntrospectableContainerInterface'    => $_bootStrapClassMapIconicPrefix . '/IntrospectableContainerInterface.php',
    'ehough_iconic_lazyproxy_phpdumper_DumperInterface' => $_bootStrapClassMapIconicPrefix . '/lazyproxy/phpdumper/DumperInterface.php',
    'ehough_iconic_lazyproxy_phpdumper_NullDumper'      => $_bootStrapClassMapIconicPrefix . '/lazyproxy/phpdumper/NullDumper.php',
    'ehough_iconic_parameterbag_ParameterBag'           => $_bootStrapClassMapIconicPrefix . '/parameterbag/ParameterBag.php',
    'ehough_iconic_parameterbag_ParameterBagInterface'  => $_bootStrapClassMapIconicPrefix . '/parameterbag/ParameterBagInterface.php',
    'ehough_iconic_Reference'                           => $_bootStrapClassMapIconicPrefix . '/Reference.php',
    'ehough_iconic_TaggedContainerInterface'            => $_bootStrapClassMapIconicPrefix . '/TaggedContainerInterface.php',

    'tubepress_api_event_EventDispatcherInterface'         => $_bootStrapClassMapSysPrefix . '/api/event/EventDispatcherInterface.php',
    'tubepress_api_ioc_CompilerPassInterface'              => $_bootStrapClassMapSysPrefix . '/api/ioc/CompilerPassInterface.php',
    'tubepress_api_ioc_ContainerInterface'                 => $_bootStrapClassMapSysPrefix . '/api/ioc/ContainerInterface.php',
    'tubepress_api_ioc_DefinitionInterface'                => $_bootStrapClassMapSysPrefix . '/api/ioc/DefinitionInterface.php',
    'tubepress_impl_boot_AbstractCachingBootHelper'        => $_bootStrapClassMapSysPrefix . '/impl/boot/AbstractCachingBootHelper.php',
    'tubepress_impl_boot_DefaultBootConfigService'         => $_bootStrapClassMapSysPrefix . '/impl/boot/DefaultBootConfigService.php',
    'tubepress_impl_boot_DefaultClassLoadingHelper'        => $_bootStrapClassMapSysPrefix . '/impl/boot/DefaultClassLoadingHelper.php',
    'tubepress_impl_environment_SimpleEnvironmentDetector' => $_bootStrapClassMapSysPrefix . '/impl/environment/SimpleEnvironmentDetector.php',
    'tubepress_impl_ioc_CoreIocContainer'                  => $_bootStrapClassMapSysPrefix . '/impl/ioc/CoreIocContainer.php',
    'tubepress_impl_ioc_Definition'                        => $_bootStrapClassMapSysPrefix . '/impl/ioc/Definition.php',
    'tubepress_impl_ioc_IconicContainer'                   => $_bootStrapClassMapSysPrefix . '/impl/ioc/IconicContainer.php',
    'tubepress_impl_ioc_IconicDefinitionWrapper'           => $_bootStrapClassMapSysPrefix . '/impl/ioc/IconicDefinitionWrapper.php',
    'tubepress_impl_ioc_Reference'                         => $_bootStrapClassMapSysPrefix . '/impl/ioc/Reference.php',
    'tubepress_impl_log_TubePressLoggingHandler'           => $_bootStrapClassMapSysPrefix . '/impl/log/TubePressLoggingHandler.php',
    'tubepress_impl_patterns_sl_ServiceLocator'            => $_bootStrapClassMapSysPrefix . '/impl/patterns/sl/ServiceLocator.php',
    'tubepress_spi_boot_AddonBooter'                       => $_bootStrapClassMapSysPrefix . '/spi/boot/AddonBooter.php',
    'tubepress_spi_boot_AddonDiscoverer'                   => $_bootStrapClassMapSysPrefix . '/spi/boot/AddonDiscoverer.php',
    'tubepress_spi_boot_BootConfigService'                 => $_bootStrapClassMapSysPrefix . '/spi/boot/BootConfigService.php',
    'tubepress_spi_boot_ClassLoadingHelper'                => $_bootStrapClassMapSysPrefix . '/spi/boot/ClassLoadingHelper.php',
    'tubepress_spi_boot_IocContainerHelper'                => $_bootStrapClassMapSysPrefix . '/spi/boot/IocContainerHelper.php',
    'tubepress_spi_environment_EnvironmentDetector'        => $_bootStrapClassMapSysPrefix . '/spi/environment/EnvironmentDetector.php',
    'tubepress_spi_version_Version'                        => $_bootStrapClassMapSysPrefix . '/spi/version/Version.php',
);