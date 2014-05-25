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
 * @covers tubepress_core_impl_listeners_html_ThumbGalleryBaseJs
 */
class tubepress_test_core_impl_listeners_html_ThumbGalleryBaseJsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_listeners_html_ThumbGalleryBaseJs
     */
    private $_sut;

    /**
     * @var tubepress_core_api_video_VideoGalleryPage
     */
    private $_providerResult;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_providerResult       = new tubepress_core_api_video_VideoGalleryPage();
        $this->_mockExecutionContext = $this->mock(tubepress_core_api_options_ContextInterface::_);
        $this->_mockEventDispatcher  = $this->mock(tubepress_core_api_event_EventDispatcherInterface::_);
        $this->_sut                  = new tubepress_core_impl_listeners_html_ThumbGalleryBaseJs($this->_mockExecutionContext, $this->_mockEventDispatcher);
    }

    public function testAlterHtml()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_api_const_options_Names::GALLERY_ID)->andReturn('gallery-id');

        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $internalEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $internalEvent->shouldReceive('getSubject')->once()->andReturn($fakeArgs);

        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(array())->andReturn($internalEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_api_const_event_EventNames::CSS_JS_GALLERY_INIT, $internalEvent);

        $event = $this->mock('tubepress_core_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn('hello');
        $event->shouldReceive('setSubject')->once()->with($this->expectedAjax());

        $this->_sut->onGalleryHtml($event);

        $this->assertTrue(true);
    }

    public function expectedAjax()
    {
        return <<<EOT
hello<script type="text/javascript">
   var tubePressDomInjector = tubePressDomInjector || [], tubePressGalleryRegistrar = tubePressGalleryRegistrar || [];
       tubePressDomInjector.push(['loadGalleryJs']);
       tubePressGalleryRegistrar.push(['register', 'gallery-id', {"yo":"mamma","is":"\"so fat\"","x":{"foo":500,"html":"<>'\""}} ]);
</script>
EOT;
    }
}