<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/player/impl/ModalPlayer.class.php';

class org_tubepress_player_impl_ModalPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpeps;
	private $_tpom;
	private $_video;
	private $_ioc;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_player_impl_ModalPlayer();
		$this->_tpeps = $this->getMock("org_tubepress_embedded_EmbeddedPlayerService");
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_video = $this->getMock('org_tubepress_video_Video');
		$this->_ioc = $this->getMock('org_tubepress_ioc_IocService');
		$this->_sut->setContainer($this->_ioc);
		$this->_sut->setOptionsManager($this->_tpom);
	}
	
	function testGetPreGalleryHtml()
	{
		$this->_tpeps->expects($this->once())
		  			 ->method("toString")
		  			 ->will($this->returnValue("fakeembedcode"));
		$this->_tpom->expects($this->exactly(1))
					->method("get")
					->will($this->returnValue(10)); 
		$this->_ioc->expects($this->once())
		           ->method('safeGet')
		           ->will($this->returnValue($this->_tpeps)); 
		  			 
		$this->assertEquals(<<<EOT
<div id="tubepress_embedded_object_12" style="display:none">fakeembedcode</div>
EOT
			, $this->_sut->getPreGalleryHtml($this->_video, 12));
	}
}
?>