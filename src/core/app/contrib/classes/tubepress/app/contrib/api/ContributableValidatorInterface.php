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
 * Validates contributables.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_app_contrib_api_ContributableValidatorInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_app_contrib_api_ContributableValidatorInterface';

    /**
     * @param tubepress_platform_api_contrib_ContributableInterface $contributable
     *
     * @return bool True if the contributable is valid. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function isValid(tubepress_platform_api_contrib_ContributableInterface $contributable);

    /**
     * @param tubepress_platform_api_contrib_ContributableInterface $contributable
     *
     * @return string|null The problem message for the given contributable, or null if no problem.
     *
     * @api
     * @since 4.0.0
     */
    function getProblemMessage(tubepress_platform_api_contrib_ContributableInterface $contributable);
}
