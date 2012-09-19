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
class tubepress_impl_factory_commands_VimeoFactoryCommandTest extends TubePressUnitTest
{
    /** @var tubepress_impl_factory_commands_VimeoFactoryCommand */
    private $_sut;

    private $_multipleFeed;

    private $_mockExecutionContext;

    public function setUp()
    {
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

        $this->_sut          = new tubepress_impl_factory_commands_VimeoFactoryCommand();
        $this->_multipleFeed = file_get_contents(dirname(__FILE__) . '/../../../../../../resources/feeds/vimeo.txt');
    }

    public function testRelativeDates()
    {
        $this->_mockExecutionContext->shouldReceive('get')->times(8)->with(tubepress_api_const_options_names_Meta::DESC_LIMIT)->andReturn(0);
        $this->_mockExecutionContext->shouldReceive('get')->times(8)->with(tubepress_api_const_options_names_Meta::RELATIVE_DATES)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->times(8)->with(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS)->andReturn(false);

        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_RAW_FEED, $this->_multipleFeed);

        $result = $this->_sut->execute($context);

        $this->assertTrue($result);

        $videos = $context->get(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_VIDEO_ARRAY);

        $this->assertTrue(is_array($videos));
        $this->assertEquals(8, count($videos));
        $video = $videos[5];
        $this->assertEquals('3 years ago', $video->getTimePublished());
    }

    public function testGetMultiple()
    {
        $this->_mockExecutionContext->shouldReceive('get')->times(8)->with(tubepress_api_const_options_names_Meta::DESC_LIMIT)->andReturn(0);
        $this->_mockExecutionContext->shouldReceive('get')->times(8)->with(tubepress_api_const_options_names_Meta::RELATIVE_DATES)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->times(8)->with(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS)->andReturn(false);

        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_RAW_FEED, $this->_multipleFeed);

        $result = $this->_sut->execute($context);

        $this->assertTrue($result);

        $videos = $context->get(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_VIDEO_ARRAY);

        $this->assertTrue(is_array($videos));
        $this->assertEquals(8, count($videos));
        $video = $videos[5];
        $this->assertEquals('makimono', $video->getAuthorDisplayName());
        $this->assertEquals('tagtool', $video->getAuthorUid());
        $this->assertEquals('', $video->getCategory());
        $this->assertEquals('N/A', $video->getCommentCount());
        $this->assertEquals('Tagtool performance by Austrian artist Die.Puntigam at Illuminating York, 30th of October 2009', $video->getDescription());
        $this->assertEquals('6:52', $video->getDuration());
        $this->assertEquals('http://vimeo.com/7416172', $video->getHomeUrl());
        $this->assertEquals('7416172', $video->getId());
        $this->assertEquals(array('Tagtool', 'Die.Puntigam', 'Illuminating York', 'Wall of Light'), $video->getKeywords());
        $this->assertEquals('2', $video->getLikesCount());
        $this->assertEquals('', $video->getRatingAverage());
        $this->assertEquals('N/A', $video->getRatingCount());
        $this->assertEquals('http://b.vimeocdn.com/ts/317/800/31780003_100.jpg', $video->getThumbnailUrl());
        $this->assertEquals('', $video->getTimeLastUpdated());
        $this->assertEquals('3 years ago', $video->getTimePublished());
        $this->assertEquals('747', $video->getViewCount());
    }
}
