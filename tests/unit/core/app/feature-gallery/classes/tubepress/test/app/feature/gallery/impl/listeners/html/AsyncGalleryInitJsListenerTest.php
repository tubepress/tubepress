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
 * @covers tubepress_app_feature_gallery_impl_listeners_html_AsyncGalleryInitJsListener<extended>
 */
class tubepress_test_app_feature_gallery_impl_listeners_html_AsyncGalleryInitJsListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_feature_gallery_impl_listeners_html_AsyncGalleryInitJsListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsReference;

    public function onSetup()
    {
        $this->_mockExecutionContext = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_mockEventDispatcher  = $this->mock(tubepress_lib_event_api_EventDispatcherInterface::_);
        $this->_mockOptionsReference = $this->mock(tubepress_app_options_api_ReferenceInterface::_);

        $this->_sut = new tubepress_app_feature_gallery_impl_listeners_html_AsyncGalleryInitJsListener(

            $this->_mockExecutionContext,
            $this->_mockEventDispatcher,
            $this->_mockOptionsReference
        );
    }

    public function testOnGalleryHtml()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_html_api_Constants::OPTION_GALLERY_ID)->andReturn('gallery-id');

        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $internalEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $internalEvent->shouldReceive('getSubject')->once()->andReturn($fakeArgs);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array())->andReturn($internalEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_feature_gallery_api_Constants::EVENT_GALLERY_INIT_JS, $internalEvent);

        $event = $this->mock('tubepress_lib_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn('hello');
        $event->shouldReceive('setSubject')->once()->with($this->_expectedAsyncJs());

        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with('yo')->andReturn(false);
        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with('is')->andReturn(true);
        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with('x')->andReturn(false);
        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with('foo')->andReturn(false);
        $this->_mockOptionsReference->shouldReceive('optionExists')->once()->with('html')->andReturn(false);

        $this->_mockOptionsReference->shouldReceive('isBoolean')->once()->with('is')->andReturn(true);

        $this->_sut->onGalleryHtml($event);

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