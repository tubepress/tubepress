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
 * Embedded player command for native Vimeo
 */
class tubepress_vimeo2_impl_listeners_embedded_EmbeddedListener
{
    private static $_URL_PARAM_AUTOPLAY  = 'autoplay';
    private static $_URL_PARAM_TITLE     = 'title';
    private static $_URL_PARAM_BYLINE    = 'byline';
    private static $_URL_PARAM_COLOR     = 'color';
    private static $_URL_PARAM_LOOP      = 'loop';
    private static $_URL_PARAM_PORTRAIT  = 'portrait';
    private static $_URL_PARAM_JS_API    = 'api';
    private static $_URL_PARAM_PLAYER_ID = 'player_id';

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_platform_api_util_LangUtilsInterface
     */
    private $_langUtils;

    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    public function __construct(tubepress_app_api_options_ContextInterface     $context,
                                tubepress_platform_api_util_LangUtilsInterface $langUtils,
                                tubepress_platform_api_url_UrlFactoryInterface $urlFactory)
    {
        $this->_context    = $context;
        $this->_langUtils  = $langUtils;
        $this->_urlFactory = $urlFactory;
    }

    public function onEmbeddedTemplateSelection(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $mediaItem tubepress_app_api_media_MediaItem
         */
        $mediaItem = $event->getArgument('mediaItem');

        if (!$this->_handlesMediaItem($mediaItem)) {

            return;
        }

        $event->setSubject('single/embedded/vimeo');
    }

    public function onEmbeddedTemplatePreRender(tubepress_lib_api_event_EventInterface $event)
    {
        $existingArgs = $event->getSubject();
        $mediaItem    = $existingArgs['mediaItem'];

        if (!$this->_handlesMediaItem($mediaItem)) {

            return;
        }

        $existingArgs[tubepress_app_api_template_VariableNames::EMBEDDED_DATA_URL] = $this->_getDataUrl($mediaItem);

        $event->setSubject($existingArgs);
    }

    private function _getDataUrl(tubepress_app_api_media_MediaItem $mediaItem)
    {
        $autoPlay = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_AUTOPLAY);
        $showInfo = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_SHOW_INFO);
        $loop     = $this->_context->get(tubepress_app_api_options_Names::EMBEDDED_LOOP);
        $color    = $this->_context->get(tubepress_vimeo2_api_Constants::OPTION_PLAYER_COLOR);

        /* build the data URL based on these options */
        $link  = $this->_urlFactory->fromString('http://player.vimeo.com/video/' . $mediaItem->getId());
        $query = $link->getQuery();

        $query->set(self::$_URL_PARAM_AUTOPLAY, $this->_langUtils->booleanToStringOneOrZero($autoPlay));
        $query->set(self::$_URL_PARAM_COLOR,    $color);
        $query->set(self::$_URL_PARAM_LOOP,     $this->_langUtils->booleanToStringOneOrZero($loop));
        $query->set(self::$_URL_PARAM_PORTRAIT, $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $query->set(self::$_URL_PARAM_BYLINE,   $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $query->set(self::$_URL_PARAM_TITLE,    $this->_langUtils->booleanToStringOneOrZero($showInfo));
        $query->set(self::$_URL_PARAM_JS_API, 1);
        $query->set(self::$_URL_PARAM_PLAYER_ID, 'tubepress-media-object-' . mt_rand());

        return $link;
    }

    private function _handlesMediaItem(tubepress_app_api_media_MediaItem $mediaItem)
    {
        /**
         * @var $provider tubepress_app_api_media_MediaProviderInterface
         */
        $provider = $mediaItem->getAttribute(tubepress_app_api_media_MediaItem::ATTRIBUTE_PROVIDER);

        return $provider->getName() === 'vimeo_v2';
    }
}
