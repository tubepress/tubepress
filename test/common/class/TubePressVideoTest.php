<?php
class TubePressVideoTest extends UnitTestCase {
    
    private $vid;
    
    private $thumbUrls = array("http://img.youtube.com/vi/m3gMgK7h-BA/2.jpg",
					"http://img.youtube.com/vi/m3gMgK7h-BA/1.jpg",
                    "http://img.youtube.com/vi/m3gMgK7h-BA/3.jpg",
                    "http://img.youtube.com/vi/m3gMgK7h-BA/0.jpg");
    
    function setUp() {
        $doc = new DOMDocument();
        $doc->load(dirname(__FILE__) . "/../../sample_feed.xml");
        $this->vid = new TubePressVideo($doc->getElementsByTagName("entry")->item(0));   
    }
    
    function testGetAuthor()
    {
        $this->assertEqual($this->vid->getAuthor(), "dhyrenz");
    }
    
    function testGetCategory()
    {
        $this->assertEqual($this->vid->getCategory(), "Music");
    }
    
    function testGetDefaultThumbURL()
    {
        $this->assertEqual($this->vid->getDefaultThumbURL(), "http://img.youtube.com/vi/m3gMgK7h-BA/2.jpg");
    }
    
    function testGetDescription()
    {
        $this->assertEqual($this->vid->getDescription(), "....one of those that will make you say...holy %$#^");
    }
    
    function testGetId()
    {
        $this->assertEqual($this->vid->getId(), "m3gMgK7h-BA");
    }
    
    function testGetRandomThumbURLGivesGoodValues()
    {
        for ($x = 0; $x < 100; $x++) {
            $this->assertTrue(array_search($this->vid->getRandomThumbURL(), $this->thumbUrls) !== FALSE);
        }
    }
    
    function testGetRatingAverage()
    {
        $this->assertEqual($this->vid->getRatingAverage(), "4.92");
    }
    
    function testGetRatingCount()
    {
        $this->assertEqual($this->vid->getRatingCount(), "29,065");
    }
    
    function testGetRandomThumbURLIsRandom()
    {      
        $hits = array(false, false, false, false);
        
        for ($x = 0; $x < 100; $x++) {
            $index = array_search($this->vid->getRandomThumbURL(), $this->thumbUrls);
            if ($index !== FALSE) {
                $hits[$index] = true;
            }
        }
        $this->assertTrue($hits[0] && $hits[1] && $hits[2] && $hits[3]);
    }
    
    function testGetRuntime()
    {
        $this->assertEqual($this->vid->getRuntime(), "2:30");
    }

    function testGetTags()
    {
        $this->assertEqual($this->vid->getTags(), "balboa feet guitar park");
    }
    
    function testGetTitle()
    {
        $this->assertEqual($this->vid->getTitle(), "amazing guitar player");
    }

    function testGetUploadTime()
    {
        $this->assertEqual($this->vid->getUploadTime(), 1161748355);
    }
    
    function testGetURL()
    {
        $this->assertEqual($this->vid->getURL(), "http://www.youtube.com/watch?v=m3gMgK7h-BA");
    }
    
    function testGetViewCount()
    {
        $this->assertEqual($this->vid->getViewCount(), "5,286,665");
    }
}
?>