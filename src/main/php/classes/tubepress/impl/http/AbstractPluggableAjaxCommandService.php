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
 * Base class for PluggableAjaxCommandService instances.
 */
abstract class tubepress_impl_http_AbstractPluggableAjaxCommandService implements tubepress_spi_http_PluggableAjaxCommandService
{
    private $_httpStatusCode = 200;

    private $_output = null;

    /**
     * Handle the Ajax request.
     *
     * @return void
     */
    public final function handle()
    {
        $result = $this->getStatusCodeToHtmlMap();

        foreach ($result as $httpStatusCode => $html) {

            $this->_httpStatusCode = $httpStatusCode;
            $this->_output         = $html;
        }
    }

    /**
     * @return integer The HTTP status code after handling this request.
     */
    public final function getHttpStatusCode()
    {
        return $this->_httpStatusCode;
    }

    /**
     * @return string The HTML output after handling this request.
     */
    public final function getOutput()
    {
        return $this->_output;
    }

    protected abstract function getStatusCodeToHtmlMap();
}

