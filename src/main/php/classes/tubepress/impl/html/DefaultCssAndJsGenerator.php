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
        return $this->_fireEventAndReturnString(tubepress_api_const_event_EventNames::CSS_JS_SCRIPT_TAG_JQUERY, '');
    }

    public function getTubePressScriptTag()
    {
        return $this->_fireEventAndReturnString(tubepress_api_const_event_EventNames::CSS_JS_SCRIPT_TAG_TUBEPRESS, '');
    }

    public function getTubePressCssTag()
    {
        return $this->_fireEventAndReturnString(tubepress_api_const_event_EventNames::CSS_JS_STYLESHEET_TAG_TUBEPRESS, '');
    }

    public function getMetaTags()
    {
        return $this->_fireEventAndReturnString(tubepress_api_const_event_EventNames::CSS_JS_META_TAGS, '');
    }

    public function getInlineCss()
    {
        return $this->_fireEventAndReturnString(tubepress_api_const_event_EventNames::CSS_JS_INLINE_CSS, '');
    }

    public function getInlineJs()
    {
        return $this->_fireEventAndReturnString(tubepress_api_const_event_EventNames::CSS_JS_INLINE_JS, '');
    }

    private function _fireEventAndReturnString($eventName, $raw)
    {
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $event           = new tubepress_api_event_TubePressEvent($raw);

        $eventDispatcher->dispatch($eventName, $event);

        return $event->getSubject();
    }
}
