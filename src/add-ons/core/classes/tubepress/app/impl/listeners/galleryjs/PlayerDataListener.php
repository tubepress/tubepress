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
class tubepress_app_impl_listeners_galleryjs_PlayerDataListener
{
    private static $_OPTIONS = 'options';
    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_api_options_ContextInterface $context)
    {
        $this->_context = $context;
    }

    public function onGalleryInitJs(tubepress_lib_api_event_EventInterface $event)
    {
        $args = $event->getSubject();

        $this->_ensureOutermostKeysExist($args);

        $options = $this->_getOptions();

        $args[self::$_OPTIONS] = array_merge($args[self::$_OPTIONS], $options);

        $event->setSubject($args);
    }

    private function _getOptions()
    {
        return array(

            tubepress_app_api_options_Names::PLAYER_LOCATION =>
                $this->_context->get(tubepress_app_api_options_Names::PLAYER_LOCATION)
        );
    }

    private function _ensureOutermostKeysExist(array &$args)
    {
        if (!isset($args[self::$_OPTIONS]) || !is_array($args[self::$_OPTIONS])) {

            $args[self::$_OPTIONS] = array();
        }
    }
}
