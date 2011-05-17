<?php

require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/factory/VideoFactoryChainContext.class.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/factory/commands/YouTubeFactoryCommand.class.php';

class org_tubepress_impl_factory_commands_YouTubeFactoryCommandTest extends TubePressUnitTest {
    
    private $_sut;
    private $_playlistFeed;
    
    function setUp()
    {
    	parent::setUp();
        $this->_playlistFeed = file_get_contents(dirname(__FILE__) . '/../feeds/playlist.xml');
        $this->_sut          = new org_tubepress_impl_factory_commands_YouTubeFactoryCommand();
    }
    
    function testGetMultiple()
    {
        $context = new org_tubepress_impl_factory_VideoFactoryChainContext($this->_playlistFeed);
        $this->_sut->execute($context);
        $result = $context->getReturnValue();
        
        $this->assertTrue(is_array($result));
        $this->assertEquals(1, count($result));
        $video = $result[0];
        $this->assertEquals('TheAkcitycouncil', $video->getAuthorDisplayName());
        $this->assertEquals('TheAkcitycouncil', $video->getAuthorUid());
        $this->assertEquals('People & Blogs', $video->getCategory());
        $this->assertEquals('N/A', $video->getCommentCount());
        $this->assertEquals("Information about shared spaces proposals in the Auckland CBD area.", $video->getDescription());
        $this->assertEquals('4:04', $video->getDuration());
        $this->assertEquals('http://www.youtube.com/watch?v=BRKWi5beywQ&amp;feature=youtube_gdata', $video->getHomeUrl());
        $this->assertEquals('BRKWi5beywQ', $video->getId());
        $this->assertEquals(array('Shared', 'Spaces', 'Upload'), $video->getKeywords());
        $this->assertEquals('N/A', $video->getLikesCount());
        $this->assertEquals('3.83', $video->getRatingAverage());
        $this->assertEquals('6', $video->getRatingCount());
        $this->assertTrue(preg_match('/http:\/\/i\.ytimg\.com\/vi\/BRKWi5beywQ\/[0123]\.jpg/', $video->getThumbnailUrl()) === 1, $video->getThumbnailUrl());
        $this->assertEquals('', $video->getTimeLastUpdated());
        $this->assertEquals('Aug 17, 2009', $video->getTimePublished());
        $this->assertEquals('1,775', $video->getViewCount());
    }
    
    function testCanHandleMultiple()
    {
        $context = new org_tubepress_impl_factory_VideoFactoryChainContext($this->_playlistFeed);
        TubePressChainTestUtils::assertCommandCanHandle($this->_sut, $context);
    }
     
    function testCannotHandle()
    {
        $context = new org_tubepress_impl_factory_VideoFactoryChainContext('bla');
        TubePressChainTestUtils::assertCommandCannotHandle($this->_sut, $context);
    }
}

