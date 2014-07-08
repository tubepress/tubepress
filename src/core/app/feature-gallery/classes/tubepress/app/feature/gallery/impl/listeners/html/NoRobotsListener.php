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
class tubepress_app_feature_gallery_impl_listeners_html_NoRobotsListener
{
    /**
     * @var tubepress_app_http_api_RequestParametersInterface
     */
    private $_requestParameters;

    public function __construct(tubepress_app_http_api_RequestParametersInterface $requestParams)
    {
        $this->_requestParameters = $requestParams;
    }

    public function onBeforeCssHtml(tubepress_lib_event_api_EventInterface $event)
    {
        $html = $event->getSubject();

        $html = $this->_getMetaTags($html);

        $event->setSubject($html);
    }

    private function _getMetaTags($html)
    {
        $page = $this->_requestParameters->getParamValueAsInt(tubepress_lib_http_api_Constants::PARAM_NAME_PAGE, 1);

        if ($page > 1) {

            $html .= "\n" . '<meta name="robots" content="noindex, nofollow" />';
        }

        return $html;
    }
}