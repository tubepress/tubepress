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
abstract class tubepress_spi_http_RequestException extends RuntimeException
{
    /**
     * Get the request that caused the exception
     *
     * @return tubepress_api_http_RequestInterface
     */
    public abstract function getRequest();

    /**
     * Get the associated response
     *
     * @return tubepress_api_http_ResponseInterface|null
     */
    public abstract function getResponse();

    /**
     * Check if a response was received
     *
     * @return bool
     */
    public function hasResponse()
    {
        return $this->getResponse() !== null;
    }
}