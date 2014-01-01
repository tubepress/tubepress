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
class tubepress_addons_core_impl_listeners_html_PreCssHtmlListener
{
    public function onBeforeCssHtml(tubepress_api_event_EventInterface $event)
    {
        $html = $event->getSubject();

        $html = $this->_addMetaTags($html);

        $event->setSubject($html);
    }

    private function _addMetaTags($html)
    {
        $qss    = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $page   = $qss->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);

        if ($page > 1) {

            $html .= "\n" . '<meta name="robots" content="noindex, nofollow" />';
        }

        return $html;
    }
}