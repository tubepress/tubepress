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
 * @covers tubepress_app_impl_listeners_template_post_GalleryPostListener<extended>
 */
class tubepress_test_app_impl_listeners_template_post_GalleryPostListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_template_post_GalleryPostListener
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
        $this->_mockExecutionContext = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockEventDispatcher  = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $this->_mockOptionsReference = $this->mock(tubepress_app_api_options_ReferenceInterface::_);

        $this->_sut = new tubepress_app_impl_listeners_template_post_GalleryPostListener(

            $this->_mockExecutionContext,
            $this->_mockEventDispatcher,
            $this->_mockOptionsReference
        );
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