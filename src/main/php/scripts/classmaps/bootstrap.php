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
    'ehough_epilog_Logger'                            => $_bootStrapClassMapEpilogPrefix . '/Logger.php',
    'ehough_epilog_LoggerFactory'                     => $_bootStrapClassMapEpilogPrefix . '/LoggerFactory.php',
    'ehough_epilog_psr_LoggerInterface'               => $_bootStrapClassMapEpilogPrefix . '/psr/LoggerInterface.php',

    'ehough_iconic_Container'                          => $_bootStrapClassMapIconicPrefix . '/Container.php',
    'ehough_iconic_ContainerInterface'                 => $_bootStrapClassMapIconicPrefix . '/ContainerInterface.php',
    'ehough_iconic_IntrospectableContainerInterface'   => $_bootStrapClassMapIconicPrefix . '/IntrospectableContainerInterface.php',
    'ehough_iconic_parameterbag_ParameterBag'          => $_bootStrapClassMapIconicPrefix . '/parameterbag/ParameterBag.php',
    'ehough_iconic_parameterbag_ParameterBagInterface' => $_bootStrapClassMapIconicPrefix . '/parameterbag/ParameterBagInterface.php',


    'tubepress_impl_boot_secondary_CachedSecondaryBootstrapper'   => $_bootStrapClassMapSysPrefix . '/impl/boot/secondary/CachedSecondaryBootstrapper.php',
    'tubepress_impl_boot_SettingsFileReader'                      => $_bootStrapClassMapSysPrefix . '/impl/boot/SettingsFileReader.php',
    'tubepress_impl_environment_SimpleEnvironmentDetector'        => $_bootStrapClassMapSysPrefix . '/impl/environment/SimpleEnvironmentDetector.php',
    'tubepress_impl_log_TubePressLoggingHandler'                  => $_bootStrapClassMapSysPrefix . '/impl/log/TubePressLoggingHandler.php',
    'tubepress_impl_patterns_sl_ServiceLocator'                   => $_bootStrapClassMapSysPrefix . '/impl/patterns/sl/ServiceLocator.php',
    'tubepress_spi_boot_secondary_SecondaryBootstrapperInterface' => $_bootStrapClassMapSysPrefix . '/spi/boot/secondary/SecondaryBootstrapperInterface.php',
    'tubepress_spi_boot_SettingsFileReaderInterface'              => $_bootStrapClassMapSysPrefix . '/spi/boot/SettingsFileReaderInterface.php',
    'tubepress_spi_environment_EnvironmentDetector'               => $_bootStrapClassMapSysPrefix . '/spi/environment/EnvironmentDetector.php',
    'tubepress_spi_version_Version'                               => $_bootStrapClassMapSysPrefix . '/spi/version/Version.php',
);