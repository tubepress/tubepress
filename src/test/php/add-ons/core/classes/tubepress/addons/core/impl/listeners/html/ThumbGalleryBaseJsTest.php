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
class tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs
     */
    private $_sut;

    /**
     * @var tubepress_api_video_VideoGalleryPage
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
        $this->_sut                  = new tubepress_addons_core_impl_listeners_html_ThumbGalleryBaseJs();
        $this->_providerResult       = new tubepress_api_video_VideoGalleryPage();
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher  = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
    }

    public function testAlterHtml()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::CSS_JS_GALLERY_INIT, ehough_mockery_Mockery::on(function ($arg) use ($fakeArgs) {

            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === array();

            $arg->setSubject($fakeArgs);

            return $good;
        }));

        $event = new tubepress_spi_event_EventBase('hello');

        $event->setArguments(array(

            'page' => 1,
            'providerName' => 'something',
            'videoGalleryPage' => $this->_providerResult
        ));

        $this->_sut->onGalleryHtml($event);

        $this->assertEquals($this->expectedAjax(), $event->getSubject());
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