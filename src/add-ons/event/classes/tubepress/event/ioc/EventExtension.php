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

class tubepress_event_ioc_EventExtension implements tubepress_spi_ioc_ContainerExtensionInterface
{
    /**
     * {@inheritdoc}
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
