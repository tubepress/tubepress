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
class org_tubepress_impl_embedded_commands_JwFlvCommandTest extends tubepress_impl_embedded_commands_AbstractEmbeddedCommandTest
{
    public function buildSut()
    {
        return new tubepress_impl_embedded_commands_JwFlvCommand();
    }

    function testCannotHandleYouTubeWithDefault()
    {
        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);

        $mockChainContext = new ehough_chaingang_impl_StandardContext();
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::YOUTUBE);
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_VIDEO_ID, 'video_id');

        $this->assertFalse($this->getSut()->execute($mockChainContext));
    }

    function testCannotHandleVimeoWithLongtail()
    {
        $mockChainContext = new ehough_chaingang_impl_StandardContext();
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::VIMEO);
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_VIDEO_ID, 'video_id');

        $this->assertFalse($this->getSut()->execute($mockChainContext));
    }

    function testCanHandleYouTubeWithLongtail()
    {
        $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');

        $this->getMockExecutionContext()->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn(tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL);

        $mockChainContext = new ehough_chaingang_impl_StandardContext();
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_PROVIDER_NAME, tubepress_spi_provider_Provider::YOUTUBE);
        $mockChainContext->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_VIDEO_ID, 'video_id');

        $this->getMockThemeHandler()->shouldReceive('getTemplateInstance')->once()->with('embedded_flash/longtail.tpl.php')->andReturn($mockTemplate);

        $this->assertTrue($this->getSut()->execute($mockChainContext));

        $this->assertEquals('http://www.youtube.com/watch?v=video_id', $mockChainContext->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_DATA_URL)->toString());
        $this->assertEquals('longtail', $mockChainContext->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_IMPLEMENTATION_NAME));
        $this->assertSame($mockTemplate, $mockChainContext->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_TEMPLATE));
    }
}

