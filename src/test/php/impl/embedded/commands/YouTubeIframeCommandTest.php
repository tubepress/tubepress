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
class tubepress_impl_embedded_commands_YouTubeIframeCommandTest extends tubepress_impl_embedded_commands_AbstractEmbeddedCommandTest
{
    public function buildSut()
    {
        return new tubepress_impl_embedded_commands_YouTubeIframeCommand();
    }

    function testCannotHandleVimeo()
    {
        $mockChainContext = new ehough_chaingang_impl_StandardContext();
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::VIMEO);
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_VIDEO_ID, 'video_id');

        $this->assertFalse($this->getSut()->execute($mockChainContext));
    }

    function testCanHandleYouTube()
    {
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(false);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_COLOR)->andReturn('123456');
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(false);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT)->andReturn('654321');
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::SHOW_RELATED)->andReturn(true);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::FULLSCREEN)->andReturn(false);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::HIGH_QUALITY)->andReturn(true);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('some-gallery-id');
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOHIDE)->andReturn(true);
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::MODEST_BRANDING)->andReturn(false);

        $mockTemplate = Mockery::mock('ehough_contemplate_api_Template');

        $mockChainContext = new ehough_chaingang_impl_StandardContext();
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::YOUTUBE);
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_VIDEO_ID, 'video_id');

        $this->getMockThemeHandler()->shouldReceive('getTemplateInstance')->once()->with('embedded_flash/youtube.tpl.php')->andReturn($mockTemplate);

        $this->assertTrue($this->getSut()->execute($mockChainContext));

        $this->assertSame($mockTemplate, $mockChainContext->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_TEMPLATE));
        $this->assertEquals('http://www.youtube.com/embed/video_id?color1=654321&color2=123456&rel=1&autoplay=0&loop=0&fs=0&showinfo=1&wmode=transparent&enablejsapi=0&autohide=1&modestbranding=0&hd=1',
            $mockChainContext->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_DATA_URL)->toString());
        $this->assertEquals('youtube', $mockChainContext->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_IMPLEMENTATION_NAME));

    }

}
