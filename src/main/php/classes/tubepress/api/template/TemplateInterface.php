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
 * A template used to render strings.
 *
 * @package TubePress\Template
 */
interface tubepress_api_template_TemplateInterface
{
    /**
     * @return array An associtiave array of template variables. May be empty but never null.
     */
    function getContext();

    /**
     * Set the variables for this template.
     *
     * @param array $context An associative array of template variables.
     *
     * @throws InvalidArgumentException If a non-associative array is passed in.
     *
     * @return void
     */
    function setContext(array $context);

    /**
     * @return string The rendered template.
     */
    function toString();
}