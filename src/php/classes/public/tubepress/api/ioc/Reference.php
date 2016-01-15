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
  * A reference to another service in the container.
  *
  * @package TubePress\IoC
  *
  * @api
  * @since 4.0.0
  */
class tubepress_api_ioc_Reference
{
    /**
     * @var string
     */
    private $_id;

    public function __construct($name)
    {
        $this->_id = strtolower($name);
    }

    /**
     * @return string The service identifier
     */
    public function __toString()
    {
        return $this->_id;
    }
}