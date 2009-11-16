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

    function testKeywords()
    {
        $this->_tpom->expects($this->any())
                    ->method('get')
                    ->will($this->returnCallback('org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_KeywordsOnly'));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $keys = $vid->getKeywords();
        $this->assertTrue(is_array($keys));
        $this->assertTrue($keys === array('Shared', 'Spaces', 'Upload'));
    }
    
    function testHomeUrl()
    {
        $this->_tpom->expects($this->any())
                    ->method('get')
                    ->will($this->returnCallback('org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_HomeUrlOnly'));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('http://www.youtube.com/watch?v=BRKWi5beywQ&amp;feature=youtube_gdata', $vid->getHomeUrl());
    }
    
    function testDuration()
    {
        $this->_tpom->expects($this->any())
                    ->method('get')
                    ->will($this->returnCallback('org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_DurationOnly'));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('4:04', $vid->getDuration());
    }
    
    function testDescriptionLimit()
    {
        $this->_tpom->expects($this->any())
                    ->method('get')
                    ->will($this->returnCallback('org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_DescriptionLimit'));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('Informatio...', $vid->getDescription());
    }
    
    function testDescriptionNoLimit()
    {
        $this->_tpom->expects($this->any())
                     ->method('get')
                     ->will($this->returnCallback('org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_DescriptionNoLimit'));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('Information about shared spaces proposals in the Auckland CBD area.', $vid->getDescription());
    }
    
    function testCategory()
    {
        $this->_tpom->expects($this->any())
                    ->method('get')
                    ->will($this->returnCallback('org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_CategoryOnly'));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('People & Blogs', $vid->getCategory());
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
            || $vid->getThumbnailUrl() === 'http://i.ytimg.com/vi/BRKWi5beywQ/1.jpg'
            || $vid->getThumbnailUrl() === 'http://i.ytimg.com/vi/BRKWi5beywQ/0.jpg';
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

function org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_KeywordsOnly()
{
    $args = func_get_args();
    $val = $args[0];
    return $val == org_tubepress_options_category_Meta::TAGS;   
}

function org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_HomeUrlOnly()
{
    $args = func_get_args();
    $val = $args[0];
    return $val == org_tubepress_options_category_Meta::URL;   
}

function org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_DurationOnly()
{
    $args = func_get_args();
    $val = $args[0];
    return $val == org_tubepress_options_category_Meta::LENGTH;
}

function org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_DescriptionNoLimit()
{
    $args = func_get_args();
    $val = $args[0];
    return $val == org_tubepress_options_category_Meta::DESCRIPTION;
}

function org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_DescriptionLimit()
{
    $args = func_get_args();
    $val = $args[0];
    if ($val == org_tubepress_options_category_Display::DESC_LIMIT) {
        return 10;
    }
    return $val == org_tubepress_options_category_Meta::DESCRIPTION;
}

function org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_CategoryOnly()
{
    $args = func_get_args();
    $val = $args[0];
    return $val == org_tubepress_options_category_Meta::CATEGORY;
}

function org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_AuthorOnly()
{
    $args = func_get_args();
    $val = $args[0];
    return $val == org_tubepress_options_category_Meta::AUTHOR;
}

function org_tubepress_video_factory_YouTubeVideoFactoryTest_callBack_RandomizeThumbsOnly()
{
    $args = func_get_args();
    $val = $args[0];
    return $val == org_tubepress_options_category_Advanced::RANDOM_THUMBS;
}
?>
