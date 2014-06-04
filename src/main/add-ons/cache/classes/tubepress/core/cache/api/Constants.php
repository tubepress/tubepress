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
 *
 */
interface tubepress_core_cache_api_Constants
{
    /**
     * @api
     * @since 3.1.0
     */
    const CLEANING_FACTOR = 'cacheCleaningFactor';

    /**
     * @api
     * @since 3.1.0
     */
    const DIRECTORY = 'cacheDirectory';

    /**
     * @api
     * @since 3.1.0
     */
    const ENABLED = 'cacheEnabled';

    /**
     * @api
     * @since 3.1.0
     */
    const LIFETIME_SECONDS = 'cacheLifetimeSeconds';
}