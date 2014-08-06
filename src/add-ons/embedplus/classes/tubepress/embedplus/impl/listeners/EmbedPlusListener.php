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
 * Plays videos with EmbedPlus.
 */
class tubepress_embedplus_impl_listeners_EmbedPlusListener
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

    public function onPlayerImplAcceptableValues(tubepress_lib_api_event_EventInterface $event)
    {
        $acceptableValues = $event->getSubject();

        if (!is_array($acceptableValues)) {

            $acceptableValues = array();
        }

        $acceptableValues['embedplus'] = 'EmbedPlus';

        $event->setSubject($acceptableValues);
    }

    public function onEmbeddedTemplateSelect(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $templateArgs array
         */
        $templateArgs = $event->getArguments();

        if (!isset($templateArgs['mediaItem'])) {

            return;
        }

        $playerImpl = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL);

        if ($playerImpl !== 'embedplus') {

            return;
        }

        /**
         * @var $mediaItem tubepress_app_api_media_MediaItem
         */
        $mediaItem = $templateArgs['mediaItem'];

        /**
         * @var $provider tubepress_app_api_media_MediaProviderInterface
         */
        $provider = $mediaItem->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER);

        if ($provider->getName() !== 'youtube_v2') {

            return;
        }

        $event->setSubject('single/embedded/embedplus');
        $event->stopPropagation();
    }

    /**
     *
     */
    public function onGalleryInitJs(tubepress_lib_api_event_EventInterface $event)
    {
        $args = $event->getSubject();

        if (!isset($args[self::$_OPTIONS][tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL])) {

            return;
        }

        $implementation = $args[self::$_OPTIONS][tubepress_app_api_options_Names::EMBEDDED_PLAYER_IMPL];

        if ($implementation !== 'embedplus') {

            return;
        }

        $existingHeight = $args[self::$_OPTIONS][tubepress_app_api_options_Names::EMBEDDED_HEIGHT];
        $newHeight      = intval($existingHeight) + 30;
        $args[self::$_OPTIONS][tubepress_app_api_options_Names::EMBEDDED_HEIGHT] = $newHeight;

        $event->setSubject($args);
    }
}