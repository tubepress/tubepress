<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/embedded/impl/DefaultEmbeddedPlayerService.class.php';

class org_tubepress_embedded_impl_DefaultEmbeddedPlayerServiceTest extends PHPUnit_Framework_TestCase {
    
    private $_sut;
    private $_vimeo;
    private $_youtube;
    private $_tpom;

	function setUp()
	{
		$this->_sut = new org_tubepress_embedded_impl_DefaultEmbeddedPlayerService();
		$this->createMocks();
		$this->applyMocks();
	}

	function testFallback()
	{
		$this->_tpom->expects($this->once())
		     ->method('calculateCurrentVideoProvider')
		     ->will($this->returnValue('something strange'));
		$this->_youtube->expects($this->once())
		     ->method('toString')
		     ->will($this->returnValue('youtube'));
		$result = $this->_sut->toString('somevideo');
		$this->assertEquals('youtube', $result);
	}

	function testYouTube()
	{
		$this->_tpom->expects($this->once())
		     ->method('calculateCurrentVideoProvider')
		     ->will($this->returnValue(org_tubepress_video_feed_provider_Provider::YOUTUBE));
		$this->_youtube->expects($this->once())
		     ->method('toString')
		     ->will($this->returnValue('youtube'));
		$result = $this->_sut->toString('somevideo');
		$this->assertEquals('youtube', $result);
	}
	
	function testVimeo()
	{
		$this->_tpom->expects($this->once())
		     ->method('calculateCurrentVideoProvider')
		     ->will($this->returnValue(org_tubepress_video_feed_provider_Provider::VIMEO));
		$this->_vimeo->expects($this->once())
		     ->method('toString')
		     ->will($this->returnValue('vimeo'));
		$result = $this->_sut->toString('somevideo');
		$this->assertEquals('vimeo', $result);
	}
	
	function applyMocks()
	{
		$this->_sut->setVimeoEmbeddedPlayerService($this->_vimeo);
		$this->_sut->setYouTubeEmbeddedPlayerService($this->_youtube);
		$this->_sut->setOptionsManager($this->_tpom);	
	}
	
	function createMocks()
	{
		$this->_vimeo = $this->getMock('org_tubepress_embedded_impl_VimeoEmbeddedPlayerService');
		$this->_youtube = $this->getMock('org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService');
		$this->_tpom = $this->getMock('org_tubepress_options_manager_OptionsManager');
	}
	
}
?>