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
 * A TubePress add-on.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_api_contrib_AddonInterface extends tubepress_api_contrib_ContributableInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_api_contrib_AddonInterface';

    /**
     * @return string[] Optional. An array of IoC container extension class names. May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    function getExtensionClassNames();

    /**
     * @return array Optional. An associative array of IoC compiler pass class names to their corresponding priorities.
     *                         Higher priorities will be processed first. May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    function getMapOfCompilerPassClassNamesToPriorities();

    /**
     * @return array Optional. An associative array of class names to the absolute path of their file locations.
     *                         May be empty, never null.
     *
     * @api
     * @since 4.0.0
     */
    function getClassMap();
}