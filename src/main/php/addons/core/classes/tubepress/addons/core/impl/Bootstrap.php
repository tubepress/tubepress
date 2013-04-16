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
class tubepress_addons_core_impl_Bootstrap
{
    public static function init()
    {
        /*
         * Register the core event handlers.
         */
        self::_registerEventListeners();
    }

    private static function _registerEventListeners()
    {
        /**
         * @var $eventDispatcher ehough_tickertape_ContainerAwareEventDispatcher
         */
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $listenerList = array(

            tubepress_api_const_event_EventNames::BOOT_COMPLETE => array(

                array('tubepress_addons_core_impl_listeners_boot_CoreOptionsRegistrar', 'onBootComplete')
            ),

            tubepress_api_const_event_EventNames::CSS_JS_STYLESHEET_TAG_TUBEPRESS => array(

                array('tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListener', 'onTubePressStylesheetTag')
            ),

            tubepress_api_const_event_EventNames::CSS_JS_META_TAGS => array(

                array('tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListener', 'onMetaTags')
            ),

            tubepress_api_const_event_EventNames::CSS_JS_SCRIPT_TAG_JQUERY => array(

                array('tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListener', 'onJqueryScriptTag')
            ),

            tubepress_api_const_event_EventNames::CSS_JS_SCRIPT_TAG_TUBEPRESS => array(

                array('tubepress_addons_core_impl_listeners_cssjs_DefaultPathsListener', 'onTubePressScriptTag')
            ),

            tubepress_api_const_event_EventNames::EMBEDDED_HTML_CONSTRUCTION => array(

                array('tubepress_addons_core_impl_listeners_embeddedhtml_PlayerJavaScriptApi', 'onEmbeddedHtml')
            ),

            tubepress_api_const_event_EventNames::EMBEDDED_TEMPLATE_CONSTRUCTION => array(

                array('tubepress_addons_core_impl_listeners_embeddedtemplate_CoreVariables', 'onEmbeddedTemplate')
            ),

            tubepress_api_const_event_EventNames::GALLERY_INIT_JS_CONSTRUCTION => array(

                array('tubepress_addons_core_impl_listeners_galleryinitjs_GalleryInitJsBaseParams', 'onGalleryInitJs')
            ),

            tubepress_api_const_event_EventNames::PLAYER_TEMPLATE_CONSTRUCTION => array(

                array('tubepress_addons_core_impl_listeners_playertemplate_CoreVariables', 'onPlayerTemplate')
            ),

            tubepress_api_const_event_EventNames::PRE_VALIDATION_OPTION_SET => array(

                array('tubepress_addons_core_impl_listeners_prevalidationoptionset_StringMagic', 'onPreValidationOptionSet'),
                array('tubepress_addons_core_impl_listeners_prevalidationoptionset_YouTubePlaylistPlPrefixRemover', 'onPreValidationOptionSet')
            ),

            tubepress_api_const_event_EventNames::SEARCH_INPUT_TEMPLATE_CONSTRUCTION => array(

                array('tubepress_addons_core_impl_listeners_searchinputtemplate_CoreVariables', 'onSearchInputTemplate')
            ),

            tubepress_api_const_event_EventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION => array(

                array('tubepress_addons_core_impl_listeners_singlevideotemplate_VideoMeta', 'onSingleVideoTemplate'),
                array('tubepress_addons_core_impl_listeners$listeners_singlevideotemplate_CoreVariables', 'onSingleVideoTemplate')
            ),

            tubepress_api_const_event_EventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION => array(

                array('tubepress_addons_core_impl_listeners_galleryhtml_GalleryJs', 'onGalleryHtml')
            ),

            tubepress_api_const_event_EventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION => array(

                array('tubepress_addons_core_impl_listeners_gallerytemplate_CoreVariables', 'onGalleryTemplate'),
                array('tubepress_addons_core_impl_listeners_gallerytemplate_EmbeddedPlayerName', 'onGalleryTemplate'),
                array('tubepress_addons_core_impl_listeners_gallerytemplate_Pagination', 'onGalleryTemplate'),
                array('tubepress_addons_core_impl_listeners_gallerytemplate_Player', 'onGalleryTemplate'),
                array('tubepress_addons_core_impl_listeners_gallerytemplate_VideoMeta', 'onGalleryTemplate'),
            ),

            tubepress_api_const_event_EventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT => array(

                array('tubepress_addons_core_impl_listeners_variablereadfromexternalinput_StringMagic', 'onIncomingInput')
            ),

            tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION => array(

                array('tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter', 'onVideoGalleryPage'),
                array('tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper', 'onVideoGalleryPage'),
                array('tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist', 'onVideoGalleryPage'),
                array('tubepress_addons_core_impl_listeners_videogallerypage_VideoPrepender', 'onVideoGalleryPage')
            ),

            ehough_shortstop_api_Events::REQUEST => array(

                array('ehough_shortstop_impl_listeners_request_RequestDefaultHeadersListener', 'onPreRequest'),
                array('ehough_shortstop_impl_listeners_request_RequestLoggingListener', 'onPreRequest')
            ),

            ehough_shortstop_api_Events::RESPONSE => array(

                array('ehough_shortstop_impl_listeners_response_ResponseDecodingListener-transfer', 'onResponse'),
                array('ehough_shortstop_impl_listeners_response_ResponseDecodingListener-content', 'onResponse'),
                array('ehough_shortstop_impl_listeners_response_ResponseLoggingListener', 'onResponse')
            )
        );

        foreach ($listenerList as $eventName => $listeners) {

            foreach ($listeners as $callback) {

                $eventDispatcher->addListenerService($eventName, $callback);
            }
        }
    }


}