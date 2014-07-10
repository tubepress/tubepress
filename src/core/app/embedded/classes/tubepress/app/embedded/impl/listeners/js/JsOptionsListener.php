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

    /**
     * @var tubepress_app_embedded_api_EmbeddedHtmlInterface
     */
    private $_embeddedHtml;

    public function __construct(tubepress_app_options_api_ContextInterface       $context,
                                tubepress_app_embedded_api_EmbeddedHtmlInterface $embeddedHtml)
    {
        $this->_context      = $context;
        $this->_embeddedHtml = $embeddedHtml;
    }

    /**
     *
     */
    public function onGalleryInitJs(tubepress_lib_event_api_EventInterface $event)
    {
        $args     = $event->getSubject();
        $page     = $event->getArgument('page');
        $implName = $this->_findMostPopularEmbeddedImpl($page);
        $options  = $this->_buildOptionsArray($implName);

        $this->_ensureOutermostKeysExist($args);

        $args[self::$_OPTIONS] = array_merge($args[self::$_OPTIONS], $options);

        $event->setSubject($args);
    }

    private function _findMostPopularEmbeddedImpl(tubepress_app_media_provider_api_Page $page)
    {
        $discovered = array();
        $items      = $page->getItems();

        if (empty($items)) {

            return null;
        }

        foreach ($items as $item) {

            $provider = $this->_embeddedHtml->getEmbeddedProvider($item)->getName();

            if (!isset($discovered[$provider])) {

                $discovered[$provider] = 1;

            } else {

                $discovered[$provider]++;
            }
        }

        arsort($discovered);

        $keys = array_keys($discovered);

        return $keys[0];
    }

    private function _ensureOutermostKeysExist(array &$args)
    {
        if (!isset($args[self::$_OPTIONS]) || !is_array($args[self::$_OPTIONS])) {

            $args[self::$_OPTIONS] = array();
        }
    }

    private function _buildOptionsArray($embeddedImplName)
    {
        $toReturn        = array();
        $requiredOptions = array(

            tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_HEIGHT,
            tubepress_app_embedded_api_Constants::OPTION_EMBEDDED_WIDTH,
        );

        foreach ($requiredOptions as $optionName) {

            $toReturn[$optionName] = $this->_context->get($optionName);
        }

        if ($embeddedImplName !== null) {

            $toReturn[tubepress_app_embedded_api_Constants::OPTION_PLAYER_IMPL] = $embeddedImplName;
        }

        return $toReturn;
    }
}