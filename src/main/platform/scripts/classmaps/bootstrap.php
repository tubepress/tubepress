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

$platformRoot = TUBEPRESS_ROOT . '/src/main/platform/classes/tubepress';
$iconicRoot   = TUBEPRESS_ROOT . '/vendor/ehough/iconic/src/main/php/ehough/iconic';
$pulsarRoot   = TUBEPRESS_ROOT . '/vendor/ehough/pulsar/src/main/php/ehough/pulsar';

return array(

    'ehough_iconic_Container'                        => $iconicRoot . '/Container.php',
    'ehough_iconic_ContainerInterface'               => $iconicRoot . '/ContainerInterface.php',
    'ehough_iconic_IntrospectableContainerInterface' => $iconicRoot . '/IntrospectableContainerInterface.php',
    'ehough_pulsar_ComposerClassLoader'              => $pulsarRoot . '/ComposerClassLoader.php',
    'ehough_pulsar_UniversalClassLoader'             => $pulsarRoot . '/UniversalClassLoader.php',
    'tubepress_api_boot_BootSettingsInterface'       => $platformRoot . '/api/boot/BootSettingsInterface.php',
    'tubepress_api_ioc_ContainerInterface'           => $platformRoot . '/api/ioc/ContainerInterface.php',
    'tubepress_api_log_LoggerInterface'              => $platformRoot . '/api/log/LoggerInterface.php',
    'tubepress_impl_boot_BootSettings'               => $platformRoot . '/impl/boot/BootSettings.php',
    'tubepress_impl_boot_helper_ContainerSupplier'   => $platformRoot . '/impl/boot/helper/ContainerSupplier.php',
    'tubepress_impl_ioc_Container'                   => $platformRoot . '/impl/ioc/Container.php',
    'tubepress_impl_log_BootLogger'                  => $platformRoot . '/impl/log/BootLogger.php',
);