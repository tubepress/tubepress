<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Base class for AjaxCommandHandler.
 */
abstract class tubepress_impl_http_AbstractPluggableAjaxCommandService implements tubepress_spi_http_PluggableAjaxCommandService
{
    private $_httpStatusCode = 200;

    private $_output = null;

    public final function handle()
    {
        $result = $this->getStatusCodeToHtmlMap();

        foreach ($result as $httpStatusCode => $html) {

            $this->_httpStatusCode = $httpStatusCode;
            $this->_output         = $html;
        }
    }

    public final function getHttpStatusCode()
    {
        return $this->_httpStatusCode;
    }

    public final function getOutput()
    {
        return $this->_output;
    }

    protected abstract function getStatusCodeToHtmlMap();
}

