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
class tubepress_app_impl_listeners_gallery_js_GalleryOptionsListener
{
    private static $_EPHEMERAL = 'ephemeral';
    private static $_OPTIONS   = 'options';

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_app_api_options_ContextInterface $context)
    {
        $this->_context = $context;
    }

    /**
     *
     */
    public function onGalleryInitJs(tubepress_lib_api_event_EventInterface $event)
    {
        $args      = $event->getSubject();
        $options   = $this->_buildOptionsArray();
        $ephemeral = $this->_context->getEphemeralOptions();

        $this->_ensureOutermostKeysExist($args);

        $args[self::$_EPHEMERAL] = array_merge($args[self::$_EPHEMERAL], $ephemeral);
        $args[self::$_OPTIONS]   = array_merge($args[self::$_OPTIONS], $options);

        $event->setSubject($args);
    }

    private function _ensureOutermostKeysExist(array &$args)
    {
        foreach (array(self::$_EPHEMERAL, self::$_OPTIONS) as $key) {

            if (!array_key_exists($key, $args) || !is_array($args[$key])) {

                $args[$key] = array();
            }
        }
    }

    private function _buildOptionsArray()
    {
        $toReturn        = array();
        $requiredOptions = array(

            tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION,
            tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS,
            tubepress_app_api_options_Names::GALLERY_AUTONEXT,
            tubepress_app_api_options_Names::HTTP_METHOD,
        );

        foreach ($requiredOptions as $optionName) {

            $toReturn[$optionName] = $this->_context->get($optionName);
        }

        return $toReturn;
    }
}