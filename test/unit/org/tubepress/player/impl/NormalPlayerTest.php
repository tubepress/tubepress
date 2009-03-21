<?php
class org_tubepress_player_impl_NormalPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpeps;
	private $_tpom;
	private $_video;
	private $_ioc;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_player_impl_NormalPlayer();
		$this->_tpeps = $this->getMock("org_tubepress_embedded_EmbeddedPlayerService");
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_video = new org_tubepress_video_Video();
		$this->_video->setTitle("Fake Video");
		$this->_ioc = $this->getMock('org_tubepress_ioc_IocService');
		$this->_sut->setContainer($this->_ioc);
	}
	
	function testGetPlayLink()
	{
		$this->_tpeps->expects($this->once())
					 ->method("applyOptions");
		$this->_tpeps->expects($this->once())
		  			 ->method("toString")
		  			 ->will($this->returnValue("fakeembedcode"));
		$this->_tpom->expects($this->exactly(2))
					->method("get")
					->will($this->returnValue(10)); 
		$this->_ioc->expects($this->once())
		           ->method('safeGet')
		           ->will($this->returnValue($this->_tpeps)); 
		  			 
		$this->assertEquals(sprintf(<<<EOT
href="#" onclick="tubePress_normalPlayer('%s', '%d', '%s')"
EOT
			, "fakeembedcode", 10, "Fake%20Video"),
			$this->_sut->getPlayLink($this->_video, $this->_tpom));
		
	}
}
?>