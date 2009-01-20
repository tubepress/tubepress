<?php
class org_tubepress_player_impl_NormalPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpeps;
	private $_tpom;
	private $_video;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_player_impl_NormalPlayer();
		$this->_tpeps = $this->getMock("TubePressEmbeddedPlayerService");
		$this->_tpom = $this->getMock("TubePressOptionsManager");
		$this->_video = new TubePressVideo();
		$this->_video->setTitle("Fake Video");
	}
	
	function testGetPlayLink()
	{
		$this->_tpeps->expects($this->once())
					 ->method("applyOptions");
		$this->_tpeps->expects($this->once())
		  			 ->method("toString")
		  			 ->will($this->returnValue("fakeembedcode"));
		$this->_tpom->expects($this->once())
					->method("get")
					->will($this->returnValue(10)); 
		  			 
		$this->_sut->setEmbeddedPlayerService($this->_tpeps);
		$this->assertEquals(sprintf(<<<EOT
href="#" onclick="tubePress_normalPlayer('%s', '%d', '%s')"
EOT
			, "fakeembedcode", 10, "Fake Video"),
			$this->_sut->getPlayLink($this->_video, $this->_tpom));
		
	}
}
?>