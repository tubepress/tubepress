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
class tubepress_plugins_core_impl_filters_galleryhtml_GalleryJsTest extends TubePressUnitTest
{
	private $_sut;

	private $_providerResult;

    private $_mockExecutionContext;

    private $_mockEventDispatcher;

    private $_mockJsonEncoder;

	function onSetup()
	{
		$this->_sut                  = new tubepress_plugins_core_impl_filters_galleryhtml_GalleryJs();
		$this->_providerResult       = new tubepress_api_video_VideoGalleryPage();
        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher  = $this->createMockSingletonService('ehough_tickertape_api_IEventDispatcher');
        $this->_mockJsonEncoder      = $this->createMockSingletonService('ehough_jameson_api_IEncoder');
	}

	function testAlterHtml()
	{
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $fakeArgs = array('yo' => 'mamma', 'is' => '"so fat"', 'x' => array('foo' => 500, 'html' => '<>\'"'));

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::GALLERY_INIT_JS_CONSTRUCTION, Mockery::on(function ($arg) use ($fakeArgs) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === array();

            $arg->setSubject($fakeArgs);

            return $good;
        }));

        $event = new tubepress_api_event_TubePressEvent('hello');

        $this->_mockJsonEncoder->shouldReceive('encode')->once()->with($fakeArgs)->andReturn('json');

        $event->setArguments(array(

            'page' => 1,
            'providerName' => 'something',
            'videoGalleryPage' => $this->_providerResult
        ));

        $this->_sut->onGalleryHtml($event);

	    $this->assertEquals($this->expectedAjax(), $event->getSubject());
	}

	function expectedAjax()
	{
	    return <<<EOT
hello
<script type="text/javascript">
	TubePressGallery.init(gallery-id, json);
</script>
EOT;
	}
}