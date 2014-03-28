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
 * Handles constructing/initializing the IOC container.
 */
interface tubepress_spi_boot_secondary_IocCompilerInterface
{
    const _ = 'tubepress_spi_boot_secondary_IocCompilerInterface';

    /**
     * Compiles the container, if necessary.
     *
     * @param tubepress_impl_ioc_IconicContainerBuilder $container
     * @param array                                     $addons
     *
     * @return void
     */
    function compile(tubepress_impl_ioc_IconicContainerBuilder $container, array $addons);
}