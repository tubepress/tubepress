<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/player/impl/YouTubePlayer.class.php';

$_SERVER = array('HTTP_USER_AGENT' => 'Mozilla');

class org_tubepress_player_impl_YouTubePlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_vid;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_player_impl_YouTubePlayer();
		$this->_vid = $this->getMock('org_tubepress_video_Video');
	}
	
	function testGetPreGalleryHtml()
	{
		$this->assertEquals("", $this->_sut->getPreGalleryHtml($this->_vid, 12));
	}
}
?>