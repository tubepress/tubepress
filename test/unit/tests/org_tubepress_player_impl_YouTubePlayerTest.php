<?php

class org_tubepress_player_impl_YouTubePlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_player_impl_YouTubePlayer();
	}
	
	function testGetPlayLink()
	{
		$vid = new org_tubepress_video_Video();
		$vid->setId("fakeid");
		$tpom = $this->getMock("TubePressOptionsManager");
		$this->assertEquals("href=\"http://youtube.com/watch?v=fakeid\"", $this->_sut->getPlayLink($vid, $tpom));
	}
}
?>