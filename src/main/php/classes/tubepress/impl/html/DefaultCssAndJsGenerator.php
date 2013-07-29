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
    public function getJqueryScriptTag()
    {
        $jQueryUrl      = $this->_getRelativeUrl('/src/main/web/vendor/jquery-1.9.1.min.js');
        $finalJQueryUrl = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::CSS_JS_SCRIPT_URL_JQUERY, $jQueryUrl);

        return $this->_getRealScriptTag($finalJQueryUrl);
    }

    public function getTubePressScriptTag()
    {
        $tubePressJsUrl = $this->_getRelativeUrl('/src/main/web/js/tubepress.js');
        $finalUrl       = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::CSS_JS_SCRIPT_URL_TUBEPRESSJS, $tubePressJsUrl);

        return $this->_getRealScriptTag($finalUrl);
    }

    public function getTubePressCssTag()
    {
        $tubePressCssUrl = $this->_getRelativeUrl('/src/main/web/css/tubepress.css');
        $finalUrl        = $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::CSS_JS_CSS_URL_TUBEPRESS, $tubePressCssUrl);

        return sprintf(sprintf('<link rel="stylesheet" href="%s" type="text/css">', $finalUrl));
    }

    public function getMetaTags()
    {
        $qss    = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $page   = $qss->getParamValueAsInt(tubepress_spi_const_http_ParamName::PAGE, 1);
        $result = $page > 1 ? '<meta name="robots" content="noindex, nofollow" />' : '';

        return $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::CSS_JS_META_TAGS, $result);
    }

    public function getInlineCss()
    {
        return $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::CSS_JS_INLINE_CSS, '');
    }

    public function getInlineJs()
    {
        return $this->_fireEventAndReturnSubject(tubepress_api_const_event_EventNames::CSS_JS_INLINE_JS, '');
    }

    private function _fireEventAndReturnSubject($eventName, $raw)
    {
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $event           = new tubepress_spi_event_EventBase($raw);

        $eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }

    private function _getRealScriptTag($url)
    {
        return sprintf('<script type="text/javascript" src="%s"></script>', $url);
    }

    private function _getRelativeUrl($url)
    {
        $environmentDetector = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $baseUrl             = $environmentDetector->getBaseUrl();

        return new ehough_curly_Url($baseUrl . $url);
    }
}
