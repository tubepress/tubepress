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
class tubepress_impl_html_DefaultHeadHtmlGenerator implements tubepress_spi_html_HeadHtmlGenerator
{
    private $_tubepressBaseUrl;

    public function __construct()
    {
        global $tubepress_base_url;
        $this->_tubepressBaseUrl = $tubepress_base_url;
    }

    public function getHeadJqueryInclusion()
    {
        $url = $this->_tubepressBaseUrl;
        return "<script type=\"text/javascript\" src=\"$url/src/main/web/js/jquery-1.8.3.min.js\"></script>";
    }

    public function getHeadInlineJs()
    {
        $url = $this->_tubepressBaseUrl;

        $executionContext = tubepress_impl_patterns_sl_ServiceLocator::getExecutionContext();
        $https            = $executionContext->get(tubepress_api_const_options_names_Advanced::HTTPS) ? 'true' : 'false';

        return <<<EOT
<script type="text/javascript">TubePressGlobalJsConfig = { baseUrl : "$url", https : $https };</script>
EOT;
    }

    public function getHeadJsIncludeString()
    {
        $url = $this->_tubepressBaseUrl;
        return "<script type=\"text/javascript\" src=\"$url/src/main/web/js/tubepress.js\"></script>";
    }

    public function getHeadCssIncludeString()
    {
        $url = $this->_tubepressBaseUrl;
        return "<link rel=\"stylesheet\" href=\"$url/src/main/web/css/tubepress.css\" type=\"text/css\" />";
    }

    public function getHeadHtmlMeta()
    {
        $qss  = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $page = $qss->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);

        return $page > 1 ? "<meta name=\"robots\" content=\"noindex, nofollow\" />" : '';
    }
}
