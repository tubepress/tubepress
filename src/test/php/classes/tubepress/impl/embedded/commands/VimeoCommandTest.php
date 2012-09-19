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
class tubepress_impl_embedded_commands_VimeoCommandTest extends tubepress_impl_embedded_commands_AbstractEmbeddedCommandTest
{
    public function buildSut() {

        return new tubepress_impl_embedded_commands_VimeoCommand();
    }

    function testCannotHandleYouTube()
    {
        $mockChainContext = new ehough_chaingang_impl_StandardContext();
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::YOUTUBE);
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_VIDEO_ID, 'video_id');


        $this->assertFalse($this->getSut()->execute($mockChainContext));
    }

    function testCanHandleVimeo()
    {
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(false);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_COLOR)->andReturn('123456');
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);

        $mockTemplate = Mockery::mock('ehough_contemplate_api_Template');

        $mockChainContext = new ehough_chaingang_impl_StandardContext();
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::VIMEO);
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_VIDEO_ID, 'video_id');

        $this->getMockThemeHandler()->shouldReceive('getTemplateInstance')->once()->with('embedded_flash/vimeo.tpl.php')->andReturn($mockTemplate);

        $this->assertTrue($this->getSut()->execute($mockChainContext));

        $this->assertSame($mockTemplate, $mockChainContext->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_TEMPLATE));
        $this->assertEquals('http://player.vimeo.com/video/video_id?autoplay=0&color=123456&loop=0&title=1&byline=1&portrait=1&api=1&player_id=tubepress-vimeo-player-video_id',
            $mockChainContext->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_DATA_URL)->toString());
        $this->assertEquals('vimeo', $mockChainContext->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_IMPLEMENTATION_NAME));
    }

}

