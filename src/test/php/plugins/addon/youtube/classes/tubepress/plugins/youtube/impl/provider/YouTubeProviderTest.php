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
class tubepress_plugins_youtube_impl_provider_YouTubeProviderTest extends TubePressUnitTest
{
    private $_sut;

    public function setUp()
    {
        $this->_sut = new tubepress_plugins_youtube_impl_provider_YouTubeProvider();
    }

    public function testGetName()
    {
        $this->assertEquals('youtube', $this->_sut->getName());
    }

    public function testGetFriendlyName()
    {
        $this->assertEquals('YouTube', $this->_sut->getFriendlyName());
    }

    public function testRecognizesVideoId()
    {
        $this->assertTrue($this->_sut->recognizesVideoId('SJxBZgC29ts'));
        $this->assertTrue($this->_sut->recognizesVideoId('S5yDS-mFRy4'));
        $this->assertTrue($this->_sut->recognizesVideoId('KcXjhikIz6o'));
        $this->assertTrue($this->_sut->recognizesVideoId('T8KJGtMGMSY'));
        $this->assertFalse($this->_sut->recognizesVideoId('339494949'));
        $this->assertFalse($this->_sut->recognizesVideoId('S5yDS-mFRy]'));
        $this->assertFalse($this->_sut->recognizesVideoId('KcXjhikIz'));
        $this->assertFalse($this->_sut->recognizesVideoId('T8K..tMGMSY'));
    }

    public function testCanPlayVideosWithPlayerImplementation()
    {
        $this->assertTrue($this->_sut->canPlayVideosWithPlayerImplementation(tubepress_api_const_options_values_PlayerImplementationValue::EMBEDPLUS));
        $this->assertTrue($this->_sut->canPlayVideosWithPlayerImplementation(tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL));
        $this->assertTrue($this->_sut->canPlayVideosWithPlayerImplementation(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED));
    }
}
