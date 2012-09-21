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
class org_tubepress_impl_shortcode_commands_SingleVideoCommandTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockExecutionContext;

    private $_mockThemeHandler;

    private $_mockEventDispatcher;

    private $_mockProviderCalculator;

    private $_mockProvider;

	function setup()
	{
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher  = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_mockThemeHandler     = Mockery::mock(tubepress_spi_theme_ThemeHandler::_);
        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);
        $this->_mockProvider = Mockery::mock(tubepress_spi_provider_Provider::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setThemeHandler($this->_mockThemeHandler);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator($this->_mockProviderCalculator);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProvider($this->_mockProvider);
        
		$this->_sut = new tubepress_impl_shortcode_commands_SingleVideoCommand();
	}

	function testExecuteNoVideo()
	{
	    $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::VIDEO)->andReturn('');


	    $this->assertFalse($this->_sut->execute(new ehough_chaingang_impl_StandardContext()));
	}

	function testExecute()
	{
	    $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::VIDEO)->andReturn('video-id');

	    $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
	    $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

	    $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('single_video.tpl.php')->andReturn($mockTemplate);

	    $this->_mockProviderCalculator->shouldReceive('calculateProviderOfVideoId')->once()->with('video-id')->andReturn('video-provider');

	    $video = new tubepress_api_video_Video();

	    $this->_mockProvider->shouldReceive('getSingleVideo')->once()->with('video-id')->andReturn($video);

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $mockTemplate;
        }));

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_HTML_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_HTML_CONSTRUCTION, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === 'template-string';
        }));

        $mockChainContext = new ehough_chaingang_impl_StandardContext();
	    $this->assertTrue($this->_sut->execute($mockChainContext));
        $this->assertEquals('template-string', $mockChainContext->get(tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain::CHAIN_KEY_GENERATED_HTML));
	}
}