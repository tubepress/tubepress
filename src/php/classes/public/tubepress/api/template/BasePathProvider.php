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
 * @api
 * @since 4.2.0
 */
class tubepress_api_template_BasePathProvider implements tubepress_spi_template_PathProviderInterface
{
    /**
     * @var string[]
     */
    private $_directories;

    public function __construct(array $directories)
    {
        $this->_directories = $directories;
    }

    /**
     * @return string[] A set of absolute filesystem directory paths
     *
     * @api
     * @since 4.0.0
     */
    public function getTemplateDirectories()
    {
        return $this->_directories;
    }
}
