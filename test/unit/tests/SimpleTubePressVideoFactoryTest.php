<?php
class SimpleTubePressVideoFactoryTest extends PHPUnit_Framework_TestCase {
    
    private $vid;
    
    private $thumbUrls = array("http://img.youtube.com/vi/m3gMgK7h-BA/2.jpg",
					"http://img.youtube.com/vi/m3gMgK7h-BA/1.jpg",
                    "http://img.youtube.com/vi/m3gMgK7h-BA/3.jpg",
                    "http://img.youtube.com/vi/m3gMgK7h-BA/0.jpg");
    
    function setUp() {
        $doc = new DOMDocument();
        $doc->load(dirname(__FILE__) . "/../sample_feed.xml");
        $factory = new SimpleTubePressVideoFactory();
        $this->vid = $factory->generate($doc->getElementsByTagName("entry")->item(0));
    }
    
    function testRetrievesAuthorFromDomElement()
    {
        $this->assertEquals($this->vid->getAuthor(), "dhyrenz");
    }
    
    function testRetrievesCategoryFromDomElement()
    {
        $this->assertEquals($this->vid->getCategory(), "Music");
    }
    
    function testRetrievesDescriptionFromDomElement()
    {
        $this->assertEquals($this->vid->getDescription(), "....one of those that will make you say...holy %$#^");
    }
    
    function testRetrievesIdFromDomElement()
    {
        $this->assertEquals($this->vid->getId(), "m3gMgK7h-BA");
    }
    
    function testRetrievesRatingAverageFromDomElement()
    {
        $this->assertEquals($this->vid->getRating(), "4.92");
    }
    
    function testRetrievesRatingCountFromDomElement()
    {
        $this->assertEquals($this->vid->getRatings(), "29,065");
    }
    
    function testRetrievesRuntimeFromDomElement()
    {
        $this->assertEquals($this->vid->getLength(), "2:30");
    }

    function testRetrievesTagsFromDomElement()
    {
    	$expectedTags = array("balboa", "feet", "guitar", "park");
        $this->assertTrue($this->vid->getTags() === $expectedTags);
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
        $this->assertEquals($this->vid->getYouTubeUrl(), "http://www.youtube.com/watch?v=m3gMgK7h-BA");
    }
    
    function testRetrievesViewCountFromDomElement()
    {
        $this->assertEquals($this->vid->getViews(), "5,286,665");
    }
}
?>