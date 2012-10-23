<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_plugins_core_Core
{
    public static function registerListeners()
    {
        $eventDispatcher = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        self::_registerBootListeners();
        self::_registerFilters($eventDispatcher);
    }

    private static function _registerBootListeners()
    {
        $optionsRegistrar = new tubepress_plugins_core_impl_listeners_CoreOptionsRegistrar();
        $optionsRegistrar->registerCoreOptions();

        $skeletonExistsListener = new tubepress_plugins_core_impl_listeners_SkeletonExistsListener();
        $skeletonExistsListener->ensureSkeletonExists();

        self::_registerShortcodeHandlers();
        self::_registerPlayerLocations();
        self::_registerAjaxCommands();
    }

    private static function _registerAjaxCommands()
    {
        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        $serviceCollectionsRegistry->registerService(

            tubepress_spi_http_PluggableAjaxCommandService::_,
            new tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandService()
        );
    }

    private static function _registerPlayerLocations()
    {
        $playerLocations = array(

            new tubepress_plugins_core_impl_player_JqModalPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_NormalPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_PopupPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_ShadowboxPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_StaticPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_VimeoPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_SoloPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_YouTubePluggablePlayerLocationService()
        );

        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        foreach ($playerLocations as $playerLocation) {

            $serviceCollectionsRegistry->registerService(

                tubepress_spi_player_PluggablePlayerLocationService::_,
                $playerLocation
            );
        }
    }

    private static function _registerShortcodeHandlers()
    {
        $thumbGalleryHandler = new tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService();
        $singleVideoHandler  = new tubepress_plugins_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService();

        $handlers = array(

            new tubepress_plugins_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService(),
            new tubepress_plugins_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService($thumbGalleryHandler),
            $singleVideoHandler,
            new tubepress_plugins_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService($singleVideoHandler),
            $thumbGalleryHandler
        );

        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        foreach ($handlers as $handler) {

            $serviceCollectionsRegistry->registerService(

                tubepress_spi_shortcode_PluggableShortcodeHandlerService::_,
                $handler
            );
        }
    }

    private static function _registerFilters(ehough_tickertape_api_IEventDispatcher $eventDispatcher)
    {
        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT,
            array(new tubepress_plugins_core_impl_filters_variablereadfromexternalinput_StringMagic(), 'onIncomingInput'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMeta(), 'onSingleVideoTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_singlevideotemplate_CoreVariables(), 'onSingleVideoTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_searchinputtemplate_CoreVariables(), 'onSearchInputTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_videogallerypage_PerPageSorter(), 'onVideoGalleryPage'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_videogallerypage_ResultCountCapper(), 'onVideoGalleryPage'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_videogallerypage_VideoBlacklist(), 'onVideoGalleryPage'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_videogallerypage_VideoPrepender(), 'onVideoGalleryPage'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET,
            array(new tubepress_plugins_core_impl_filters_prevalidationoptionset_StringMagic(), 'onPreValidationOptionSet'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET,
            array(new tubepress_plugins_core_impl_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover(), 'onPreValidationOptionSet'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::PLAYER_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_playertemplate_CoreVariables(), 'onPlayerTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_gallerytemplate_CoreVariables(), 'onGalleryTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_gallerytemplate_EmbeddedPlayerName(), 'onGalleryTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_gallerytemplate_Pagination(), 'onGalleryTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_gallerytemplate_Player(), 'onGalleryTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_gallerytemplate_VideoMeta(), 'onGalleryTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::GALLERY_INIT_JS_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_galleryinitjs_GalleryInitJsBaseParams(), 'onGalleryInitJs'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_galleryhtml_GalleryJs(), 'onGalleryHtml'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::EMBEDDED_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_embeddedtemplate_CoreVariables(), 'onEmbeddedTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::EMBEDDED_HTML_CONSTRUCTION,
            array(new tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi(), 'onEmbeddedHtml'));
    }
}

tubepress_plugins_core_Core::registerListeners();