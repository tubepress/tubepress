<?php

require_once dirname(__FILE__) . '/../../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/video/factory/impl/YouTubeVideoFactory.class.php';

class org_tubepress_video_factory_YouTubeVideoFactoryTest extends TubePressUnitTest {
    
    private $_sut;
    private $_playlistFeed;
    
    function setUp() {
	$this->initFakeIoc();
        $this->_sampleFeedOne = file_get_contents(dirname(__FILE__) . '/playlist.xml');
        $this->_sut = new org_tubepress_video_factory_impl_YouTubeVideoFactory();
        org_tubepress_log_Log::setEnabled(false, array());
    }

    function testViews()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Meta::VIEWS => true));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('1,775', $vid->getViewCount());
    }
    
    function testTimeUploadedRelative()
    {
        $this->setOptions(array(
            org_tubepress_api_const_options_Meta::UPLOADED => true,
            org_tubepress_api_const_options_Display::RELATIVE_DATES => true
        ));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('1 year ago', $vid->getTimePublished());
    }
    
    function testTimeUploadedAbsolute()
    {
       $this->setOptions(array(org_tubepress_api_const_options_Meta::UPLOADED => true,
        org_tubepress_api_const_options_Advanced::DATEFORMAT => 'l jS \of F Y h:i:s A'));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('Monday 17th of August 2009 04:36:21 PM', $vid->getTimePublished());
    }
    
    function testRating()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Meta::RATING => true));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('3.83', $vid->getRatingAverage());
    }
    
    function testRatings()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Meta::RATINGS => true));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals(6, $vid->getRatingCount());
    }
    
    function testKeywords()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Meta::TAGS => true));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $keys = $vid->getKeywords();
        $this->assertTrue(is_array($keys));
        $this->assertTrue($keys === array('Shared', 'Spaces', 'Upload'));
    }
    
    function testHomeUrl()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Meta::URL => true));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('http://www.youtube.com/watch?v=BRKWi5beywQ&amp;feature=youtube_gdata', $vid->getHomeUrl());
    }
    
    function testDuration()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Meta::LENGTH => true));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('4:04', $vid->getDuration());
    }
    
    function testDescriptionLimit()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Meta::DESCRIPTION => true,
            org_tubepress_api_const_options_Display::DESC_LIMIT => 10));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('Informatio...', $vid->getDescription());
    }
    
    function testDescriptionNoLimit()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Meta::DESCRIPTION => true));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('Information about shared spaces proposals in the Auckland CBD area.', $vid->getDescription());
    }
    
    function testCategory()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Meta::CATEGORY => true));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('People & Blogs', $vid->getCategory());
    }
    
    function testAuthor()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Meta::AUTHOR => true));
        $results = $this->_sut->feedToVideoArray($this->_sampleFeedOne, 1000);
        $vid = $results[0];
        $this->assertEquals('TheAkcitycouncil', $vid->getAuthorUid());
    }
    
    function testRandomThumbnailUrl()
    {
        $this->setOptions(array(org_tubepress_api_const_options_Display::RANDOM_THUMBS => true));
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
        $this->setOptions(array(org_tubepress_api_const_options_Display::RANDOM_THUMBS => false));
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
        $this->assertEquals('BRKWi5beywQ', $results[0]->getId());
    }
}

?>
