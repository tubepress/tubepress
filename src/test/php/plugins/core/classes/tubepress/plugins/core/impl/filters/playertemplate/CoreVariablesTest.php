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
class tubepress_plugins_core_impl_filters_playertemplate_CoreVariablesTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockExecutionContext;

    private $_mockEmbeddedHtmlGenerator;

    public function onSetup()
    {
        $this->_sut = new tubepress_plugins_core_impl_filters_playertemplate_CoreVariables();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $this->_mockEmbeddedHtmlGenerator = $this->createMockSingletonService(tubepress_spi_embedded_EmbeddedHtmlGenerator::_);
    }

    function testAlterTemplate()
    {
        $video = new tubepress_api_video_Video();
        $video->setId('video-id');

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('gallery-id');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)->andReturn(668);

        $this->_mockEmbeddedHtmlGenerator->shouldReceive('getHtml')->once()->with('video-id')->andReturn('embedded-html');

        $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::EMBEDDED_SOURCE, 'embedded-html');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::GALLERY_ID, 'gallery-id');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::VIDEO, $video);
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::EMBEDDED_WIDTH, 668);

        $event = new tubepress_api_event_TubePressEvent($mockTemplate);
        $event->setArguments(array(

            'playerName' => 'player-name',
            'providerName' => 'provider-name',
            'video' => $video
        ));

        $this->_sut->onPlayerTemplate($event);

        $this->assertEquals($mockTemplate, $event->getSubject());
    }
}