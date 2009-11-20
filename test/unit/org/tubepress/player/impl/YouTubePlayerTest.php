<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/player/impl/YouTubePlayer.class.php';

class org_tubepress_player_impl_YouTubePlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_vid;
	private $_bd;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_player_impl_YouTubePlayer();
		$this->_bd = $this->getMock('org_tubepress_browser_BrowserDetector');
		$this->_vid = $this->getMock('org_tubepress_video_Video');
		$this->_sut->setBrowserDetector($this->_bd);
	}
	
	function testGetPreGalleryHtml()
	{
	    $this->_bd->expects($this->once())
                  ->method('detectBrowser')
                  ->will($this->returnValue(org_tubepress_browser_BrowserDetector::UNKNOWN));
		$this->assertEquals("", $this->_sut->getPreGalleryHtml($this->_vid, 12));
	}
}
?>