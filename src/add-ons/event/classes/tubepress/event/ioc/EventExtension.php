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
 *
 */
class tubepress_event_ioc_EventExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * Called during construction of the TubePress service container. If an add-on intends to add
     * services to the container, it should do so here. The incoming `tubepress_api_ioc_ContainerBuilderInterface`
     * will be completely empty, and after this method is executed will be merged into the primary service container.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder An empty `tubepress_api_ioc_ContainerBuilderInterface` instance.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder)
    {
        $containerBuilder->register(
            'container_aware_event_dispatcher',
            'Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher'
        )->addArgument(new tubepress_api_ioc_Reference('symfony_service_container'));

        $containerBuilder->register(
            tubepress_api_event_EventDispatcherInterface::_,
            'tubepress_event_impl_tickertape_EventDispatcher'
        )->addArgument(new tubepress_api_ioc_Reference('container_aware_event_dispatcher'));
    }
}