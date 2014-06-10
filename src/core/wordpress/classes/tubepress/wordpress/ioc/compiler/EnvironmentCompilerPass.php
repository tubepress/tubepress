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
 */
class tubepress_wordpress_ioc_compiler_EnvironmentCompilerPass implements tubepress_api_ioc_CompilerPassInterface
{
    private static $_REQUIRED_SERVICE_IDS = array(

        tubepress_core_environment_api_EnvironmentInterface::_,
        tubepress_wordpress_impl_wp_WpFunctions::_
    );

    /**
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        foreach (self::$_REQUIRED_SERVICE_IDS as $requiredId) {

            if (!$containerBuilder->hasDefinition($requiredId)) {

                return;
            }
        }

        $environment = $containerBuilder->getDefinition(tubepress_core_environment_api_EnvironmentInterface::_);
        $environment->addMethodCall('setWpFunctionsInterface', array(new tubepress_api_ioc_Reference(tubepress_wordpress_impl_wp_WpFunctions::_)));
    }
}