<?php

require_once dirname(__FILE__) . '/../../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/factory/strategies/YouTubeFactoryStrategy.class.php';

class org_tubepress_impl_factory_strategies_YouTubeFactoryStrategyTest extends TubePressUnitTest {
    
    private $_sut;
    private $_playlistFeed;
    
    function setUp() {
    	$this->initFakeIoc();
        $this->_playlistFeed = file_get_contents(dirname(__FILE__) . '/../feeds/playlist.xml');
        $this->_sut = new org_tubepress_impl_factory_strategies_YouTubeFactoryStrategy();
        org_tubepress_impl_log_Log::setEnabled(false, array());
    }
    
    function testGetMultiple()
    {
        $result = $this->_sut->execute($this->_playlistFeed);
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
        $this->assertEquals('http://i.ytimg.com/vi/BRKWi5beywQ/2.jpg', $video->getThumbnailUrl());
        $this->assertEquals('', $video->getTimeLastUpdated());
        $this->assertEquals('Aug 17, 2009', $video->getTimePublished());
        $this->assertEquals('1,775', $video->getViewCount());
    }
    
    function testStart()
    {
        $this->_sut->start();
    }

    function testStop()
    {
        $this->_sut->stop();
    }

    /**
     * @expectedException Exception
     */
    function testExecNoArgs()
    {
        $this->_sut->execute();
    }
    
    function testCanHandleMultiple()
    {
        $this->assertTrue($this->_sut->canHandle($this->_playlistFeed));
    }
     
    function testCannotHandle()
    {
        $this->assertFalse($this->_sut->canHandle('bla'));
    }
}

