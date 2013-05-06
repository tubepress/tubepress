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
class tubepress_addons_core_impl_BootstrapTest extends tubepress_test_TubePressUnitTest
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

            tubepress_api_const_event_EventNames::HTML_EMBEDDED => array(

                array('tubepress_addons_core_impl_listeners_html_EmbeddedPlayerApiJs', 'onEmbeddedHtml')
            ),

            tubepress_api_const_event_EventNames::TEMPLATE_EMBEDDED => array(

                array('tubepress_addons_core_impl_listeners_template_EmbeddedCoreVariables', 'onEmbeddedTemplate')
            ),

            tubepress_api_const_event_EventNames::CSS_JS_GALLERY_INIT => array(

                array('tubepress_addons_core_impl_listeners_cssjs_GalleryInitJsBaseParams', 'onGalleryInitJs')
            ),

            tubepress_api_const_event_EventNames::TEMPLATE_PLAYERLOCATION => array(

                array('tubepress_addons_core_impl_listeners_template_PlayerLocationCoreVariables', 'onPlayerTemplate')
            ),

            tubepress_api_const_event_EventNames::OPTIONS_NVP_PREVALIDATIONSET => array(

                array('tubepress_addons_core_impl_listeners_options_PreValidationOptionSetStringMagic', 'onPreValidationOptionSet'),
            ),

            tubepress_api_const_event_EventNames::TEMPLATE_SEARCH_INPUT => array(

                array('tubepress_addons_core_impl_listeners_template_SearchInputCoreVariables', 'onSearchInputTemplate')
            ),

            tubepress_api_const_event_EventNames::TEMPLATE_SINGLE_VIDEO => array(

                array('tubepress_addons_core_impl_listeners_template_SingleVideoMeta', 'onSingleVideoTemplate'),
                array('tubepress_addons_core_impl_listeners_template_SingleVideoCoreVariables', 'onSingleVideoTemplate')
            ),

            tubepress_api_const_event_EventNames::HTML_THUMBNAIL_GALLERY => array(

                array('tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs', 'onGalleryHtml')
            ),

            tubepress_api_const_event_EventNames::TEMPLATE_THUMBNAIL_GALLERY => array(

                array('tubepress_addons_core_impl_listeners_template_ThumbGalleryCoreVariables', 'onGalleryTemplate'),
                array('tubepress_addons_core_impl_listeners_template_ThumbGalleryEmbeddedImplName', 'onGalleryTemplate'),
                array('tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination', 'onGalleryTemplate'),
                array('tubepress_addons_core_impl_listeners_template_ThumbGalleryPlayerLocation', 'onGalleryTemplate'),
                array('tubepress_addons_core_impl_listeners_template_ThumbGalleryVideoMeta', 'onGalleryTemplate'),
            ),

            tubepress_api_const_event_EventNames::OPTIONS_NVP_READFROMEXTERNAL => array(

                array('tubepress_addons_core_impl_listeners_options_ExternalInputStringMagic', 'onIncomingInput')
            ),

            tubepress_api_const_event_EventNames::VIDEO_GALLERY_PAGE => array(

                array('tubepress_addons_core_impl_listeners_videogallerypage_PerPageSorter', 'onVideoGalleryPage'),
                array('tubepress_addons_core_impl_listeners_videogallerypage_ResultCountCapper', 'onVideoGalleryPage'),
                array('tubepress_addons_core_impl_listeners_videogallerypage_VideoBlacklist', 'onVideoGalleryPage'),
                array('tubepress_addons_core_impl_listeners_videogallerypage_VideoPrepender', 'onVideoGalleryPage')
            ),

            tubepress_api_const_event_EventNames::CSS_JS_INLINE_JS => array(

                array('tubepress_addons_core_impl_listeners_html_JsConfig', 'onInlineJs')
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