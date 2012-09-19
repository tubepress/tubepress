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
class org_tubepress_impl_factory_commands_YouTubeFactoryCommandTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_factory_commands_YouTubeFactoryCommand
     */
    private $_sut;

    private $_playlistFeed;

    private $_mockExecutionContext;

    public function setUp()
    {
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

        $this->_playlistFeed = file_get_contents(dirname(__FILE__) . '/../../../../../../resources/feeds/playlist.xml');
        $this->_sut          = new tubepress_impl_factory_commands_YouTubeFactoryCommand();
    }

    public function testGetMultiple()
    {

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::DESC_LIMIT)->andReturn(0);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::RELATIVE_DATES)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::DATEFORMAT)->andReturn('M j, Y');

        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_RAW_FEED, $this->_playlistFeed);

        $result = $this->_sut->execute($context);

        $this->assertTrue($result);

        $videos = $context->get(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_VIDEO_ARRAY);

        $this->assertTrue(is_array($videos));
        $this->assertEquals(1, count($videos));
        $video = $videos[0];
        $this->assertEquals('3hough', $video->getAuthorDisplayName());
        $this->assertEquals('3hough', $video->getAuthorUid());
        $this->assertEquals('Music', $video->getCategory());
        $this->assertEquals('N/A', $video->getCommentCount());
        $this->assertEquals(<<<EOT
A mashup song by ComaR, of The Jackson 5's "I Want You Back" and Justice's "D.A.N.C.E." I just made the video.

-FAIR USE-
"Copyright Disclaimer Under Section 107 of the Copyright Act 1976, allowance is made for "fair use" for purposes such as criticism, comment, news reporting, teaching, scholarship, and research. Fair use is a use permitted by copyright statute that might otherwise be infringing. Non-profit, educational or personal use tips the balance in favor of fair use."
EOT
        , $video->getDescription());
        $this->assertEquals('2:43', $video->getDuration());
        $this->assertEquals('http://www.youtube.com/watch?v=zfaMzjDAGuA&feature=youtube_gdata', $video->getHomeUrl());
        $this->assertEquals('zfaMzjDAGuA', $video->getId());
        $this->assertEquals(array('Jackson 5', 'Justice', 'Michael Jackson'), $video->getKeywords());
        $this->assertEquals('N/A', $video->getLikesCount());
        $this->assertEquals('5.00', $video->getRatingAverage());
        $this->assertEquals('30', $video->getRatingCount());
        $this->assertTrue(preg_match('/http:\/\/i\.ytimg\.com\/vi\/zfaMzjDAGuA\/(?:[0123]|default)\.jpg/', $video->getThumbnailUrl()) === 1, $video->getThumbnailUrl());
        $this->assertEquals('', $video->getTimeLastUpdated());
        $this->assertEquals('Sep 18, 2009', $video->getTimePublished());
        $this->assertEquals('10,778', $video->getViewCount());
    }

    public function testCanHandleMultiple()
    {
        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_RAW_FEED, $this->_playlistFeed);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::DESC_LIMIT)->andReturn(0);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::RELATIVE_DATES)->andReturn(false);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Meta::DATEFORMAT)->andReturn('M j, Y');

        $this->assertTrue($this->_sut->execute($context));
    }

    public function testCannotHandle()
    {
        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_RAW_FEED, 'abc');

        $this->assertFalse($this->_sut->execute($context));
    }
}

