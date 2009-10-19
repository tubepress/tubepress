<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/video/Video.class.php';

class org_tubepress_video_VideoTest extends PHPUnit_Framework_TestCase {
    
	private $_vid;
	
	function setUp()
	{
		$this->_vid = new org_tubepress_video_Video();
	}
	
	function testSetGetAuthor()
	{
		$this->_vid->setAuthor("hough");
		$this->assertEquals($this->_vid->getAuthor(), "hough");
	}
	
	function testSetGetCategory()
	{
		$this->_vid->setCategory("Sports");
		$this->assertEquals($this->_vid->getCategory(), "Sports");
	}
	
	function testSetGetThumbUrls()
	{
		$thumbs = array("1.jpg", "2.jpg", "3.jpg");
		$this->_vid->setRegularQualityThumbnailUrls($thumbs);
		$this->assertTrue($this->_vid->getRegularQualityThumbnailUrls() === $thumbs);
	}
	
	function testSetGetDescription()
	{ 	
		$this->_vid->setDescription("fake");
		$this->assertEquals($this->_vid->getDescription(), "fake");
	}
	
	function testSetGetId() 
	{
		$this->_vid->setId("ERERKJKFF");
		$this->assertEquals($this->_vid->getId(), "ERERKJKFF");
	}
	
	function testSetGetRatingAverage() 
	{
		$this->_vid->setRatingAverage("4.5");
		$this->assertEquals($this->_vid->getRatingAverage(), "4.5");
	}
	
	function testSetGetRatingCount() 
	{
		$this->_vid->setRatingCount("33000");
		$this->assertEquals($this->_vid->getRatingCount(), "33000");
	}
	
	function testSetgetDuration() 
	{
		$this->_vid->setDuration("3:12");
		$this->assertEquals($this->_vid->getDuration(), "3:12");
	}
	
	function testSetGetKeywords() 
	{
		$tags = array("one", "two", "three");
		$this->_vid->setKeywords($tags);
		$this->assertTrue($this->_vid->getKeywords() === $tags);
	}
	
	function testSetGetTitle() 
	{ 		
		$this->_vid->setTitle("Mr. Title");
		$this->assertEquals($this->_vid->getTitle(), "Mr. Title");
	}
	
	function testSetGetTimePublished() 
	{
		$this->_vid->setTimePublished("112233");
		$this->assertEquals($this->_vid->getTimePublished(), "112233");
	}
	
	function testSetgetHomeUrl() 
	{ 	
		$this->_vid->setHomeUrl("http://youtube.com");
		$this->assertEquals($this->_vid->getHomeUrl(), "http://youtube.com");
	}
	
	function testSetGetViewCount() 
	{
		$this->_vid->setViewCount("12000");
		$this->assertEquals($this->_vid->getViewCount(), "12000");
	}
	
	function testgetDefaultThumbnailUrl()
	{
		$this->_vid->setId("34");
		$this->assertEquals($this->_vid->getDefaultThumbnailUrl(), "http://img.youtube.com/vi/34/default.jpg");
	}
	
	public static function getFakeInstance($random)
	{
		$vid = new org_tubepress_video_Video();
		$vid->setAuthor("3hough");
		$vid->setDisplayable(true);
		return $vid;
	}
}
?>