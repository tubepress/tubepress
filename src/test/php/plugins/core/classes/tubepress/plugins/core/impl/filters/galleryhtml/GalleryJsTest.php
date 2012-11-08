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