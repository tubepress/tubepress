<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/api/video/Video.class.php';
require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_api_video_VideoTest extends TubePressUnitTest {
    
	private $_vid;
	
	function setUp()
	{
		$this->_vid = new org_tubepress_api_video_Video();
	}
	
	function testSetGetAuthor()
	{
		$this->_vid->setAuthorUid('hough');
		$this->assertEquals($this->_vid->getAuthorUid(), 'hough');
	}
	
	function testSetGetCategory()
	{
		$this->_vid->setCategory('Sports');
		$this->assertEquals($this->_vid->getCategory(), 'Sports');
	}
	
   function testSetGetCommentCount()
    {
        $this->_vid->setCommentCount(2);
        $this->assertEquals($this->_vid->getCommentCount(), 2);
    }
	
	function testSetGetDescription()
	{ 	
		$this->_vid->setDescription('fake');
		$this->assertEquals($this->_vid->getDescription(), 'fake');
	}
	
   function testSetGetDuration() 
    {
        $this->_vid->setDuration('3:12');
        $this->assertEquals($this->_vid->getDuration(), '3:12');
    }
    
   function testSetGetHomeUrl() 
    {
        $this->_vid->setHomeUrl('http://youtube.com');
        $this->assertEquals($this->_vid->getHomeUrl('http://youtube.com'), 'http://youtube.com');
    }
	
	function testSetGetId() 
	{
		$this->_vid->setId('ERERKJKFF');
		$this->assertEquals($this->_vid->getId(), 'ERERKJKFF');
	}
	
   function testSetGetKeywords() 
    {
        $tags = 'one two three';
        $this->_vid->setKeywords($tags);
        $this->assertEquals($this->_vid->getKeywords(), $tags);
    }
	
	function testSetGetRatingAverage() 
	{
		$this->_vid->setRatingAverage('4.5');
		$this->assertEquals($this->_vid->getRatingAverage(), '4.5');
	}
	
	function testSetGetRatingCount() 
	{
		$this->_vid->setRatingCount('33000');
		$this->assertEquals($this->_vid->getRatingCount(), '33000');
	}
	
    function testSetGetThumbnailUrl()
    {
        $this->_vid->setThumbnailUrl('thumburl');
        $this->assertEquals($this->_vid->getThumbnailUrl(), 'thumburl');
    }
	
    function testSetGetTimeLastUpdated() 
    {
        $this->_vid->setTimeLastUpdated('212233');
        $this->assertEquals($this->_vid->getTimeLastUpdated(), '212233');
    }
    
    function testSetGetTimePublished() 
    {
        $this->_vid->setTimePublished('112233');
        $this->assertEquals($this->_vid->getTimePublished(), '112233');
    }
	
	function testSetGetTitle() 
	{ 		
		$this->_vid->setTitle('Mr. Title');
		$this->assertEquals($this->_vid->getTitle(), 'Mr. Title');
	}

	function testSetGetViewCount() 
	{
		$this->_vid->setViewCount('12000');
		$this->assertEquals($this->_vid->getViewCount(), '12000');
	}
}
?>
