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
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_core_api_template_TemplateInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_core_api_template_TemplateInterface';

    /**
     * @return array An associtiave array of template variables. May be empty but never null.
     *
     * @api
     * @since 4.0.0
     */
    function getVariables();

    /**
     * @param string $name
     *
     * @return mixed The current value for the given variable name. May be null.
     *
     * @api
     * @since 4.0.0
     */
    function getVariable($name);

    /**
     * @param string $name
     *
     * @return bool True if the template has something set for the given variable, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function hasVariable($name);

    /**
     * Set the variables for this template.
     *
     * @param array $context An associative array of template variables.
     *
     * @throws InvalidArgumentException If a non-associative array is passed in.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function setVariables(array $context);

    /**
     * @param string $name  The name of the variable to set.
     * @param mixed  $value The value of the variable.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function setVariable($name, $value);

    /**
     * @return string The rendered template.
     *
     * @api
     * @since 4.0.0
     */
    function toString();
}