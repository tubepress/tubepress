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

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::BOOT,
            array(new tubepress_plugins_core_listeners_CoreOptionsRegistrar(), 'onBoot'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::BOOT,
            array(new tubepress_plugins_core_listeners_SkeletonExistsListener(), 'onBoot'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT,
            array(new tubepress_plugins_core_filters_variablereadfromexternalinput_StringMagic(), 'onIncomingInput'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_singlevideotemplate_VideoMeta(), 'onSingleVideoTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_singlevideotemplate_CoreVariables(), 'onSingleVideoTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_searchinputtemplate_CoreVariables(), 'onSearchInputTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_videogallerypage_PerPageSorter(), 'onVideoGalleryPage'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_videogallerypage_ResultCountCapper(), 'onVideoGalleryPage'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_videogallerypage_VideoBlacklist(), 'onVideoGalleryPage'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_videogallerypage_VideoPrepender(), 'onVideoGalleryPage'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET,
            array(new tubepress_plugins_core_filters_prevalidationoptionset_StringMagic(), 'onPreValidationOptionSet'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET,
            array(new tubepress_plugins_core_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover(), 'onPreValidationOptionSet'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::PLAYER_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_playertemplate_CoreVariables(), 'onPlayerTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_gallerytemplate_CoreVariables(), 'onGalleryTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_gallerytemplate_EmbeddedPlayerName(), 'onGalleryTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_gallerytemplate_Pagination(), 'onGalleryTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_gallerytemplate_Player(), 'onGalleryTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_gallerytemplate_VideoMeta(), 'onGalleryTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::GALLERY_INIT_JS_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_galleryinitjs_GalleryInitJsBaseParams(), 'onGalleryInitJs'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_galleryhtml_GalleryJs(), 'onGalleryHtml'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::EMBEDDED_TEMPLATE_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_embeddedtemplate_CoreVariables(), 'onEmbeddedTemplate'));

        $eventDispatcher->addListener(tubepress_api_const_event_CoreEventNames::EMBEDDED_HTML_CONSTRUCTION,
            array(new tubepress_plugins_core_filters_embeddedhtml_PlayerJavaScriptApi(), 'onEmbeddedHtml'));
    }
}

tubepress_plugins_core_Core::registerListeners();