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
interface tubepress_core_api_contrib_ContributableValidatorInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_api_contrib_ContributableValidatorInterface';

    /**
     * @param tubepress_api_contrib_ContributableInterface $contributable
     *
     * @return bool True if the contributable is valid. False otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function isValid(tubepress_api_contrib_ContributableInterface $contributable);

    /**
     * @param tubepress_api_contrib_ContributableInterface $contributable
     *
     * @return string|null The problem message for the given contributable, or null if no problem.
     *
     * @api
     * @since 4.0.0
     */
    function getProblemMessage(tubepress_api_contrib_ContributableInterface $contributable);
}
