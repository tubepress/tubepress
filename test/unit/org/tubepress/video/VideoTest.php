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
		$this->_vid->setThumbUrls($thumbs);
		$this->assertTrue($this->_vid->getThumbUrls() === $thumbs);
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
	
	function testSetGetRating() 
	{
		$this->_vid->setRating("4.5");
		$this->assertEquals($this->_vid->getRating(), "4.5");
	}
	
	function testSetGetRatings() 
	{
		$this->_vid->setRatings("33000");
		$this->assertEquals($this->_vid->getRatings(), "33000");
	}
	
	function testSetGetLength() 
	{
		$this->_vid->setLength("3:12");
		$this->assertEquals($this->_vid->getLength(), "3:12");
	}
	
	function testSetGetTags() 
	{
		$tags = array("one", "two", "three");
		$this->_vid->setTags($tags);
		$this->assertTrue($this->_vid->getTags() === $tags);
	}
	
	function testSetGetTitle() 
	{ 		
		$this->_vid->setTitle("Mr. Title");
		$this->assertEquals($this->_vid->getTitle(), "Mr. Title");
	}
	
	function testSetGetUploadTime() 
	{
		$this->_vid->setUploadTime("112233");
		$this->assertEquals($this->_vid->getUploadTime(), "112233");
	}
	
	function testSetGetYouTubeUrl() 
	{ 	
		$this->_vid->setYouTubeUrl("http://youtube.com");
		$this->assertEquals($this->_vid->getYouTubeUrl(), "http://youtube.com");
	}
	
	function testSetGetViews() 
	{
		$this->_vid->setViews("12000");
		$this->assertEquals($this->_vid->getViews(), "12000");
	}
	
	function testGetDefaultThumbUrl()
	{
		$this->_vid->setId("34");
		$this->assertEquals($this->_vid->getDefaultThumbUrl(), "http://img.youtube.com/vi/34/default.jpg");
	}
	
	function testGetRandomThumbUrl()
	{
		$thumbs = array("1.jpg", "2.jpg", "3.jpg");
		$this->_vid->setThumbUrls($thumbs);
        $this->assertTrue(in_array($this->_vid->getRandomThumbUrl(), $thumbs));
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