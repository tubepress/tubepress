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
class tubepress_core_media_gallery_impl_listeners_html_NoRobotsListener
{
    /**
     * @var tubepress_core_http_api_RequestParametersInterface
     */
    private $_requestParametersInterface;

    public function __construct(tubepress_core_http_api_RequestParametersInterface $requestParams)
    {
        $this->_requestParametersInterface = $requestParams;
    }

    public function onBeforeCssHtml(tubepress_core_event_api_EventInterface $event)
    {
        $html = $event->getSubject();

        $html = $this->_addMetaTags($html);

        $event->setSubject($html);
    }

    private function _addMetaTags($html)
    {
        $page = $this->_requestParametersInterface->getParamValueAsInt(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1);

        if ($page > 1) {

            $html .= "\n" . '<meta name="robots" content="noindex, nofollow" />';
        }

        return $html;
    }
}