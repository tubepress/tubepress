<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_gallery_impl_listeners_GalleryListener<extended>
 */
class tubepress_test_gallery_impl_listeners_GalleryListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_gallery_impl_listeners_GalleryListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLogger;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRequestParams;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCollector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaPage;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaItem;

    public function onSetup()
    {
        $this->_mockLogger           = $this->mock(tubepress_platform_api_log_LoggerInterface::_);
        $this->_mockExecutionContext = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockRequestParams    = $this->mock(tubepress_lib_api_http_RequestParametersInterface::_);
        $this->_mockCollector        = $this->mock(tubepress_app_api_media_CollectorInterface::_);
        $this->_mockTemplating       = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);
        $this->_mockEventDispatcher  = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $this->_mockOptionsReference = $this->mock(tubepress_app_api_options_ReferenceInterface::_);
        $this->_mockEvent            = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockMediaPage        = $this->mock('tubepress_app_api_media_MediaPage');
        $this->_mockMediaItem        = $this->mock('tubepress_app_api_media_MediaItem');

        $this->_sut = new tubepress_gallery_impl_listeners_GalleryListener(

            $this->_mockLogger,
            $this->_mockExecutionContext,
            $this->_mockRequestParams,
            $this->_mockCollector,
            $this->_mockTemplating,
            $this->_mockEventDispatcher,
            $this->_mockOptionsReference
        );
    }

    public function testGalleryInitJs()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_AUTONEXT)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::HTTP_METHOD)->andReturn('HELLO');
        $this->_mockExecutionContext->shouldReceive('getEphemeralOptions')->once()->andReturn(array('x' => 'y', 'foo' => 'bar'));

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array('yo' => 'mamma'));
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(

            'yo' => 'mamma',

            'options' => array(

                tubepress_app_api_options_Names::GALLERY_AJAX_PAGINATION => true,
                tubepress_app_api_options_Names::GALLERY_FLUID_THUMBS    => false,
                tubepress_app_api_options_Names::GALLERY_AUTONEXT        => true,
                tubepress_app_api_options_Names::HTTP_METHOD             => 'HELLO',
            ),

            'ephemeral' => array(

                'x'   => 'y',
                'foo' => 'bar'
            )
        ));

        $this->_sut->onGalleryInitJs($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnGalleryTemplatePreRender()
    {
        $expected = array(
            tubepress_app_api_template_VariableNames::HTML_WIDGET_ID              => 47,
            tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_WIDTH_PX  => 556,
            tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_HEIGHT_PX => 984,
        );

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array('mediaPage' => $this->_mockMediaPage));
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array_merge(array(
            'mediaPage' => $this->_mockMediaPage,
            tubepress_app_api_template_VariableNames::HTML_WIDGET_ID => 47,
            tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_WIDTH_PX => 556,
            tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_HEIGHT_PX => 984,
        ), $expected));

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::HTML_GALLERY_ID)->andReturn(47);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH)->andReturn(556);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT)->andReturn(984);

        $this->_sut->onGalleryTemplatePreRender($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function testOnPostGalleryTemplateRender()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::HTML_GALLERY_ID)->andReturn('gallery-id');

        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $internalEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $internalEvent->shouldReceive('getSubject')->once()->andReturn($fakeArgs);

        $mockPage = $this->mock('tubepress_app_api_media_MediaPage');

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array(), array(
            'mediaPage'  => $mockPage,
            'pageNumber' => 12
        ))->andReturn($internalEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::GALLERY_INIT_JS, $internalEvent);

        $event = $this->mock('tubepress_lib_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn('hello');
        $event->shouldReceive('getArgument')->once()->with('mediaPage')->andReturn($mockPage);
        $event->shouldReceive('getArgument')->once()->with('pageNumber')->andReturn(12);
        $event->shouldReceive('setSubject')->once()->with($this->_expectedAsyncJs());

        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with('yo')->andReturn(false);
        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with('is')->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with('x')->andReturn(false);
        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with('foo')->andReturn(false);
        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with('html')->andReturn(false);

        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with('is')->andReturn(true);

        $this->_sut->onPostGalleryTemplateRender($event);

        $this->assertTrue(true);
    }

    public function _expectedAsyncJs()
    {
        return <<<EOT
hello<script type="text/javascript">
   var tubePressDomInjector = tubePressDomInjector || [], tubePressGalleryRegistrar = tubePressGalleryRegistrar || [];
       tubePressDomInjector.push(['loadGalleryJs']);
       tubePressGalleryRegistrar.push(['register', 'gallery-id', {"yo":"mamma","is":true,"x":{"foo":500,"html":"<>'\""}} ]);
</script>
EOT;
    }
}