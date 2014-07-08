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
interface tubepress_lib_template_api_TemplateFactoryInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_lib_template_api_TemplateFactoryInterface';

    /**
     * Loads a new template instance by path.
     *
     * @param string[] $paths An array of filesystem paths to search, in order of priority. The first path
     *                        with an existing file will be used. Each path can either be absolute or relative.
     *                        If absolute, the absolute path will be used. If relative, assume path is
     *                        relative to the root of the current TubePress theme.
     *
     * @return tubepress_lib_template_api_TemplateInterface|null A template instance, or null if the template cannot be found.
     *
     * @api
     * @since 4.0.0
     */
    function fromFilesystem(array $paths);
}