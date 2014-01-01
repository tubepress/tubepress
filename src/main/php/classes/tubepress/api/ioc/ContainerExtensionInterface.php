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

interface tubepress_api_ioc_ContainerExtensionInterface
{
    const _ = 'tubepress_api_ioc_ContainerExtensionInterface';

    const TAG_EVENT_LISTENER             = 'tubepress.event.listener';
    const TAG_TAGGED_SERVICES_CONSUMER   = 'tubepress.consumer.taggedServices';

    /**
     * Allows extensions to load services into the TubePress IOC container.
     *
     * @param tubepress_api_ioc_ContainerInterface $container A tubepress_api_ioc_ContainerInterface instance.
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    function load(tubepress_api_ioc_ContainerInterface $container);
}