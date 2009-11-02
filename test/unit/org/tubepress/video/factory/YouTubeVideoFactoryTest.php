<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/video/factory/YouTubeVideoFactory.class.php';

class org_tubepress_video_factory_YouTubeVideoFactoryTest extends PHPUnit_Framework_TestCase {
    
    private $_vids;
    
    private $thumbUrls = array("http://img.youtube.com/vi/m3gMgK7h-BA/2.jpg",
					"http://img.youtube.com/vi/m3gMgK7h-BA/1.jpg",
                    "http://img.youtube.com/vi/m3gMgK7h-BA/3.jpg",
                    "http://img.youtube.com/vi/m3gMgK7h-BA/0.jpg");
    
    function setUp() {
        $doc = file_get_contents(dirname(__FILE__) . "/../../../../sample_feed.xml");
        $factory = new org_tubepress_video_factory_YouTubeVideoFactory();
        $factory->setLog($this->getMock('org_tubepress_log_Log'));
        $this->_vids = $factory->feedToVideoArray($doc, 1);
    }
    
    function testRetrievesAuthorFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getAuthor(), "u4ever12344");
    }
    
    function testRetrievesCategoryFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getCategory(), "Music");
    }
    
    function testRetrievesDescriptionFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getDescription(), "Music video for the third single, You Belong With Me, from Taylor Swift&#039;s upcoming 2nd album, Fearless, out 11/11. You&#039;re on the phone with your girlfriend, She&#039;s upset She&#039;s going off about something that you said She doesnt get your humour like I do I&#039;m in the room, its a typical Tuesday night I&#039;m listening to the kind of music she doesnt like And she&#039;ll never know your story like I do But she wears short skirts, I wear t-shirts She&#039;s cheer captain and I&#039;m on the bleachers Dreaming bout ...");
    }
    
    function testRetrievesIdFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getId(), "5AHzIq_n-DQ");
    }
    
    function testRetrievesRatingAverageFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getRatingAverage(), "4.840202");
    }
    
    function testRetrievesRatingCountFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getRatingCount(), "17,422");
    }
    
    function testRetrievesRuntimeFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getDuration(), "3:49");
    }

    function testRetrievesKeywordsFromDomElement()
    {
    	$expectedKeywords = array("country", "taylor", "swift", "you", 
    	   "belong", "with", "me", "official", "music", "video", 
    	       "new", "single", "fearless", "lyrics");
        $this->assertTrue($this->_vids[0]->getKeywords() === $expectedKeywords);
    }
    
    function testRetrievesTitleFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getTitle(), "Taylor Swift - You Belong With Me - Official Music Video");
    }

    function testRetrievesUploadTimeFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getTimePublished(), 1242100279);
    }
    
    function testRetrievesUrlFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getHomeUrl(), "http://www.youtube.com/watch?v=5AHzIq_n-DQ&amp;feature=youtube_gdata");
    }
    
    function testRetrievesViewCountFromDomElement()
    {
        $this->assertEquals($this->_vids[0]->getViewCount(), "6,816,621");
    }
}
?>
