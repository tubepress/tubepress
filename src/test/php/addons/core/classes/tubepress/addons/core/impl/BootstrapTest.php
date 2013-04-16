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
class tubepress_addons_core_impl_BootstrapTest extends TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsDescriptorReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFileSystem;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFinderFactory;

    public function onSetup()
    {
        $this->_mockEventDispatcher            = $this->createMockSingletonService('ehough_tickertape_EventDispatcherInterface');
        $this->_mockOptionsDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockEnvironmentDetector        = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockFileSystem                 = $this->createMockSingletonService('ehough_filesystem_FilesystemInterface');
        $this->_mockFinderFactory              = $this->createMockSingletonService('ehough_finder_FinderFactoryInterface');

        if (!defined('ABSPATH')) {

            define('ABSPATH', '/value-of-abspath/');
        }
    }

    public function testCore()
    {
        $this->_testEventListeners();

        require TUBEPRESS_ROOT . '/src/main/php/addons/core/scripts/bootstrap.php';

        $this->assertTrue(true);
    }

    private function _testEventListeners()
    {
        $listenerList = array(

            tubepress_api_const_event_EventNames::BOOT_COMPLETE => array(

                array('tubepress_addons_core_impl_listeners_boot_CoreOptionsRegistrar', 'onBootComplete')
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

            foreach ($listeners as $listenerData) {

                $this->_mockEventDispatcher->shouldReceive('addListenerService')->once()->with($eventName, $listenerData);
            }
        }
    }

}