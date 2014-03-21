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
  * @package TubePress\IOC
  */
interface tubepress_api_ioc_CompilerPassInterface
{
    /**
     * Provides add-ons with the ability to modify the TubePress IOC container builder
     * before it is compiled for production.
     *
     * @param tubepress_api_ioc_ContainerBuilderInterface The core IOC container builder.
     *
     * @api
     * @since 3.1.0
     */
    function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder);
}