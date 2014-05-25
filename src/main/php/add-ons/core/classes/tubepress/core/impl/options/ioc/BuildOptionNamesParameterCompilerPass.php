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
 * Finds registered listeners and adds them to the event dispatcher.
 */
class tubepress_core_impl_options_ioc_BuildOptionNamesParameterCompilerPass implements tubepress_api_ioc_CompilerPassInterface
{
    /**
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    public function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $providerIds = $containerBuilder->findTaggedServiceIds(tubepress_core_api_options_ProviderInterface::_);
        $optionNames = array();

        foreach ($providerIds as $serviceId => $tags) {

            /**
             * @var $provider tubepress_core_api_options_ProviderInterface
             */
            $provider = $containerBuilder->get($serviceId);

            $optionNames = array_merge($optionNames, $provider->getAllOptionNames());
        }

        $containerBuilder->setParameter('tubePressOptionNames', $optionNames);
    }
}