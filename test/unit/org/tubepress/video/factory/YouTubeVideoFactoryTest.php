<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/video/factory/YouTubeVideoFactory.class.php';

class org_tubepress_video_factory_YouTubeVideoFactoryTest extends PHPUnit_Framework_TestCase {
    
    private $_sut;
    private $_playlistFeed;
    private $_tpom;
    
    function setUp() {
        $this->_sampleFeedOne = file_get_contents(dirname(__FILE__) . '/playlist.xml');
        $this->_sut = new org_tubepress_video_factory_YouTubeVideoFactory();
        $this->_sut->setLog($this->getMock('org_tubepress_log_Log'));
        $this->_tpom = $this->getMock('org_tubepress_options_manager_OptionsManager');
        $this->_sut->setOptionsManager($this->_tpom);
    }

    function testAuthor()
    {
        $this->_tpom->expects($this->any())
                    ->method('get')
                    ->will($this->returnCallback('org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_AuthorOnly'));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('TheAkcitycouncil', $vid->getAuthor());
    }
    
    function testRandomThumbnailUrl()
    {
        $this->_tpom->expects($this->any())
                    ->method('get')
                    ->will($this->returnCallback('org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_RandomizeThumbsOnly'));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $ok = $vid->getThumbnailUrl() === 'http://i.ytimg.com/vi/BRKWi5beywQ/2.jpg'
            || $vid->getThumbnailUrl() === 'http://i.ytimg.com/vi/BRKWi5beywQ/3.jpg'
            || $vid->getThumbnailUrl() === 'http://i.ytimg.com/vi/BRKWi5beywQ/1.jpg';
        if (!$ok) {
            print "Got " . $vid->getThumbnailUrl() . " as thumb URL";
        }
        $this->assertTrue($ok);
    }
    
    function testDefaultThumbnailUrl()
    {
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('http://i.ytimg.com/vi/BRKWi5beywQ/2.jpg', $vid->getThumbnailUrl());
    }
    
    function testId()
    {
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('BRKWi5beywQ', $vid->getId());
    }
    
    function testTitle()
    {
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('Auckland City Council shared space proposals', $vid->getTitle());
    }
    
    /**
     * @expectedException Exception
     */
    function testBadXml()
    {
        $this->_sut->feedToVideoArray('fake feed', 20);
    }
    
    function testEmptyDocument()
    {
        $results = $this->_sut->feedToVideoArray('<?xml version="1.0" encoding="ISO-8859-1"?><nothing/>', 20);
        $this->assertTrue(is_array($results));
        $this->assertEquals(0, count($results));
    }
    
    function testFirstVideoNotAvailable()
    {
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $this->assertEquals(1, count($results));
    }
    
    
}

function org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_AuthorOnly() {
    $args = func_get_args();
    $val = $args[0];
    return $val == org_tubepress_options_category_Meta::AUTHOR;
}

function org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_RandomizeThumbsOnly() {
    $args = func_get_args();
    $val = $args[0];
    return $val == org_tubepress_options_category_Advanced::RANDOM_THUMBS;
}
?>
