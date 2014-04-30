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
  * Allows add-ons to load services into the TubePress service container.
  *
  * @package TubePress\IoC
  */
interface tubepress_api_ioc_ContainerExtensionInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_ioc_ContainerExtensionInterface';

    const TAG_EVENT_LISTENER           = 'tubepress.event.listener';
    const TAG_TAGGED_SERVICES_CONSUMER = 'tubepress.consumer.taggedServices';

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
     * @since 3.2.0
     */
    function load(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder);
}