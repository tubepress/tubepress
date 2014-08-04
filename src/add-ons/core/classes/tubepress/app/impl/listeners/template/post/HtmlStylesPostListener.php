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
class tubepress_app_impl_listeners_template_post_HtmlStylesPostListener
{
    /**
     * @var tubepress_lib_api_http_RequestParametersInterface
     */
    private $_requestParameters;

    public function __construct(tubepress_lib_api_http_RequestParametersInterface $requestParams)
    {
        $this->_requestParameters = $requestParams;
    }

    public function onPostGalleryTemplateRender(tubepress_lib_api_event_EventInterface $event)
    {
        $html = $event->getSubject();

        $html = $this->_getMetaTags($html);

        $event->setSubject($html);
    }

    private function _getMetaTags($html)
    {
        $page = $this->_requestParameters->getParamValueAsInt('tubepress_page', 1);

        if ($page > 1) {

            $html .= "\n" . '<meta name="robots" content="noindex, nofollow" />';
        }

        return $html;
    }
}