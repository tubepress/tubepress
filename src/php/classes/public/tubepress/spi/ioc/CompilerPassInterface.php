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
  * Provides add-ons with the ability to modify the primary {@link tubepress_api_ioc_ContainerBuilderInterface}
  * *as* it is compiled for production. Add-ons may add, remove, or modify service definitions or parameters.
  *
  * @package TubePress\IoC
  *
  * @api
  * @since 4.0.0
  */
interface tubepress_spi_ioc_CompilerPassInterface
{
    /**
     * @param tubepress_api_ioc_ContainerBuilderInterface $containerBuilder The primary service container builder.
     *
     * @api
     * @since 4.0.0
     */
    function process(tubepress_api_ioc_ContainerBuilderInterface $containerBuilder);
}