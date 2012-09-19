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
class tubepress_plugins_core_filters_galleryhtml_GalleryJsTest extends PHPUnit_Framework_TestCase
{
	private $_sut;

	private $_providerResult;

    private $_mockExecutionContext;

    private $_mockEventDispatcher;

	function setup()
	{
		$this->_sut = new tubepress_plugins_core_filters_galleryhtml_GalleryJs();
		$this->_providerResult = new tubepress_api_video_VideoGalleryPage();

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

        $this->_mockEventDispatcher = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
	}

	function testAlterHtml()
	{
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_GalleryInitJsConstruction::EVENT_NAME, Mockery::on(function ($arg) {

            $good = $arg instanceof tubepress_api_event_GalleryInitJsConstruction && $arg->getSubject() === array();

            $arg->setParams(array('yo' => 'mamma', 'is' => '"so fat"'));

            return $good;
        }));

        $event = new tubepress_api_event_ThumbnailGalleryHtmlConstruction('hello');

        $event->setArguments(array(

            tubepress_api_event_ThumbnailGalleryHtmlConstruction::ARGUMENT_PAGE => 1,
            tubepress_api_event_ThumbnailGalleryHtmlConstruction::ARGUMENT_PROVIDER_NAME => 'something',
            tubepress_api_event_ThumbnailGalleryHtmlConstruction::ARGUMENT_VIDEO_GALLERY_PAGE => $this->_providerResult
        ));

        $this->_sut->onGalleryHtml($event);

	    $this->assertEquals($this->expectedAjax(), $event->getSubject());
	}

	function expectedAjax()
	{
	    return <<<EOT
hello
<script type="text/javascript">
	TubePressGallery.init(gallery-id, {
		yo : mamma,
		is : "so fat"
	});
</script>
EOT;
	}

    function tearDown()
    {
        Mockery::close();
    }
}