<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_wordpress_ioc_compiler_EnvironmentCompilerPass implements tubepress_spi_ioc_CompilerPassInterface
{
    private static $_REQUIRED_SERVICE_IDS = array(

        tubepress_api_environment_EnvironmentInterface::_,
        tubepress_wordpress_impl_wp_WpFunctions::_,
    );

    /**
     * {@inheritdoc}
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        foreach (self::$_REQUIRED_SERVICE_IDS as $requiredId) {

            if (!$containerBuilder->hasDefinition($requiredId)) {

                return;
            }
        }

        $environment = $containerBuilder->getDefinition(tubepress_api_environment_EnvironmentInterface::_);
        $environment->addMethodCall('setWpFunctionsInterface', array(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_)));
    }
}
