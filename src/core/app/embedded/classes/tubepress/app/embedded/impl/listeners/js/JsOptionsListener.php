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
class tubepress_app_embedded_impl_listeners_js_JsOptionsListener
{
    private static $_OPTIONS = 'options';

    /**
     * @var tubepress_app_options_api_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_options_api_ContextInterface $context)
    {
        $this->_context = $context;
    }

    /**
     *
     */
    public function onGalleryInitJs(tubepress_lib_event_api_EventInterface $event)
    {
        $args    = $event->getSubject();
        $options = $this->_buildOptionsArray();

        $this->_ensureOutermostKeysExist($args);

        $args[self::$_OPTIONS] = array_merge($args[self::$_OPTIONS], $options);

        $event->setSubject($args);
    }

    private function _ensureOutermostKeysExist(array &$args)
    {
        if (!isset($args[self::$_OPTIONS]) || !is_array($args[self::$_OPTIONS])) {

            $args[self::$_OPTIONS] = array();
        }
    }

    private function _buildOptionsArray()
    {
        $toReturn        = array();
        $requiredOptions = array(

            tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT,
            tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_WIDTH,
            tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL,
        );

        foreach ($requiredOptions as $optionName) {

            $toReturn[$optionName] = $this->_context->get($optionName);
        }

        return $toReturn;
    }
}