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
class tubepress_plugins_core_CoreTest extends TubePressUnitTest
{
	private $_mockEventDispatcher;

	function setup()
	{
		$this->_mockEventDispatcher = Mockery::mock('ehough_tickertape_api_IEventDispatcher');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
	}

	function testCore()
    {
        $expected = array(

            array(tubepress_api_const_event_CoreEventNames::BOOT =>
                array('tubepress_plugins_core_impl_listeners_CoreOptionsRegistrar', 'onBoot')),

            array(tubepress_api_const_event_CoreEventNames::BOOT =>
                array('tubepress_plugins_core_impl_listeners_SkeletonExistsListener', 'onBoot')),

            array(tubepress_api_const_event_CoreEventNames::BOOT =>
                array('tubepress_plugins_core_impl_listeners_ShortcodeHandlersRegistrar', 'onBoot')),

            array(tubepress_api_const_event_CoreEventNames::BOOT =>
                array('tubepress_plugins_core_impl_listeners_AjaxCommandsRegistrar', 'onBoot')),

            array(tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT =>
                array('tubepress_plugins_core_impl_filters_variablereadfromexternalinput_StringMagic', 'onIncomingInput')),

            array(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMeta', 'onSingleVideoTemplate')),

            array(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_singlevideotemplate_CoreVariables', 'onSingleVideoTemplate')),

            array(tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_searchinputtemplate_CoreVariables', 'onSearchInputTemplate')),

            array(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_videogallerypage_PerPageSorter', 'onVideoGalleryPage')),

            array(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_videogallerypage_ResultCountCapper', 'onVideoGalleryPage')),

            array(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_videogallerypage_VideoBlacklist', 'onVideoGalleryPage')),

            array(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_videogallerypage_VideoPrepender', 'onVideoGalleryPage')),

            array(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET =>
                array('tubepress_plugins_core_impl_filters_prevalidationoptionset_StringMagic', 'onPreValidationOptionSet')),

            array(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET =>
                array('tubepress_plugins_core_impl_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover', 'onPreValidationOptionSet')),

            array(tubepress_api_const_event_CoreEventNames::PLAYER_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_playertemplate_CoreVariables', 'onPlayerTemplate')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_gallerytemplate_CoreVariables', 'onGalleryTemplate')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_gallerytemplate_EmbeddedPlayerName', 'onGalleryTemplate')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_gallerytemplate_Pagination', 'onGalleryTemplate')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_gallerytemplate_Player', 'onGalleryTemplate')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_gallerytemplate_VideoMeta', 'onGalleryTemplate')),

            array(tubepress_api_const_event_CoreEventNames::GALLERY_INIT_JS_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_galleryinitjs_GalleryInitJsBaseParams', 'onGalleryInitJs')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_galleryhtml_GalleryJs', 'onGalleryHtml')),

            array(tubepress_api_const_event_CoreEventNames::EMBEDDED_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_embeddedtemplate_CoreVariables', 'onEmbeddedTemplate')),

            array(tubepress_api_const_event_CoreEventNames::EMBEDDED_HTML_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi', 'onEmbeddedHtml'))
        );

        $eventArray = array();

        foreach ($expected as $expect) {

            $eventName = array_keys($expect);
            $eventName = $eventName[0];

            if (! isset($eventArray[$eventName])) {

                $eventArray[$eventName] = array();
            }

            $eventArray[$eventName][] = $expect[$eventName];
        }

        foreach ($eventArray as $eventName => $callbacks) {

            $this->_mockEventDispatcher->shouldReceive('addListener')->times(count($callbacks))->with(

                $eventName, Mockery::on(function ($arr) use ($callbacks) {

                    foreach ($callbacks as $callback) {

                        if ($arr[0] instanceof $callback[0] && $arr[1] === $callback[1]) {

                            return true;
                        }
                    }

                    return false;
                }));
        }

        require __DIR__ . '/../../../../main/php/plugins/core/Core.php';

        $this->assertTrue(true);
    }
}