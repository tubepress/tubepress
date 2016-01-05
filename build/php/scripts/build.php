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

require __DIR__ . '/../../../vendor/autoload.php';

$container = new \Symfony\Component\DependencyInjection\ContainerBuilder();
$locator   = new \Symfony\Component\Config\FileLocator(__DIR__ . '/../../config');
$loader    = new \Symfony\Component\DependencyInjection\Loader\YamlFileLoader($container, $locator);

$loader->load('services.yml');
$loader->load('parameters.yml');

$container->get('builder')->build();