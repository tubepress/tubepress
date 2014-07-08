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
 * A registry of contributables.
 *
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
     * @return tubepress_platform_api_contrib_ContributableInterface[] May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    function getAll();
}