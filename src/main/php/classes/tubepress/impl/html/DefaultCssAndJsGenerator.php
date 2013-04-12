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
 * Generates HTML for use in the <head>.
 */
class tubepress_impl_html_DefaultCssAndJsGenerator implements tubepress_spi_html_CssAndJsGenerator
{
    private $_tubepressBaseUrl;

    public function __construct()
    {
        global $tubepress_base_url;

        $this->_tubepressBaseUrl = $tubepress_base_url;
    }

    public function getJqueryScriptTag()
    {
        return sprintf('<script type="text/javascript" src="%s/src/main/web/vendor/jquery-1.8.3.min.js"></script>', $this->_tubepressBaseUrl);
    }

    public function getTubePressScriptTag()
    {
        return sprintf('<script type="text/javascript" src="%s/src/main/web/js/tubepress.js"></script>', $this->_tubepressBaseUrl);
    }

    public function getTubePressCssTag()
    {
        return sprintf('<link rel="stylesheet" href="%s/src/main/web/css/tubepress.css" type="text/css" />', $this->_tubepressBaseUrl);
    }

    public function getMetaTags()
    {
        $qss  = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $page = $qss->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);

        return $page > 1 ? '<meta name="robots" content="noindex, nofollow" />' : '';
    }

    public function getInlineCss()
    {
        return '';
    }

    public function getInlineJs()
    {
        return '';
    }
}
