<?php
class TubePressVideoTest extends PHPUnit_Framework_TestCase {
    
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
    
    function testRetrievesAuthorFromDomElement()
    {
        $this->assertEquals($this->vid->getAuthor(), "dhyrenz");
    }
    
    function testRetrievesCategoryFromDomElement()
    {
        $this->assertEquals($this->vid->getCategory(), "Music");
    }
    
    function testRetrievesDefaultThumbUrlFromDomElement()
    {
        $this->assertEquals($this->vid->getDefaultThumbURL(), "http://img.youtube.com/vi/m3gMgK7h-BA/2.jpg");
    }
    
    function testRetrievesDescriptionFromDomElement()
    {
        $this->assertEquals($this->vid->getDescription(), "....one of those that will make you say...holy %$#^");
    }
    
    function testRetrievesIdFromDomElement()
    {
        $this->assertEquals($this->vid->getId(), "m3gMgK7h-BA");
    }
    
    function testCanProvideRandomThumbnailUrls()
    {
        for ($x = 0; $x < 100; $x++) {
            $this->assertTrue(array_search($this->vid->getRandomThumbURL(), $this->thumbUrls) !== FALSE);
        }
    }
    
    function testRetrievesRatingAverageFromDomElement()
    {
        $this->assertEquals($this->vid->getRatingAverage(), "4.92");
    }
    
    function testRetrievesRatingCountFromDomElement()
    {
        $this->assertEquals($this->vid->getRatingCount(), "29,065");
    }
    
    function testProvidesTrulyRandomThumbnailUrls()
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
    
    function testRetrievesRuntimeFromDomElement()
    {
        $this->assertEquals($this->vid->getRuntime(), "2:30");
    }

    function testRetrievesTagsFromDomElement()
    {
        $this->assertEquals($this->vid->getTags(), "balboa feet guitar park");
    }
    
    function testRetrievesTitleFromDomElement()
    {
        $this->assertEquals($this->vid->getTitle(), "amazing guitar player");
    }

    function testRetrievesUploadTimeFromDomElement()
    {
        $this->assertEquals($this->vid->getUploadTime(), 1161748355);
    }
    
    function testRetrievesUrlFromDomElement()
    {
        $this->assertEquals($this->vid->getURL(), "http://www.youtube.com/watch?v=m3gMgK7h-BA");
    }
    
    function testRetrievesViewCountFromDomElement()
    {
        $this->assertEquals($this->vid->getViewCount(), "5,286,665");
    }
}
?>