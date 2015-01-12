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
 * @package TubePress\Template
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_lib_api_template_TemplatingInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_lib_api_template_TemplatingInterface';

    /**
     * Renders the template with the given context and returns it as string.
     *
     * @param string $name         The name of the template to render.
     * @param array  $templateVars An array of parameters to pass to the template
     *
     * @return string The rendered template
     */
    function renderTemplate($name, array $templateVars = array());
}