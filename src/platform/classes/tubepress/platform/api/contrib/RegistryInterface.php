<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @api
 * @since 4.0.0
 */
interface tubepress_platform_api_contrib_RegistryInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_platform_api_contrib_RegistryInterface';

    /**
     * @return tubepress_platform_api_contrib_ContributableInterface[]
     *
     * @api
     * @since 4.0.0
     */
    function getAll();

    /**
     * @param $name string The name of the contributable to return.
     *
     * @return tubepress_platform_api_contrib_ContributableInterface
     *
     * @api
     * @since 4.0.0
     */
    function getInstanceByName($name);
}