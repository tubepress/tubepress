<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";

class TPGreyBoxPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	
	function setUp()
	{
		$this->_sut = new TPGreyBoxPlayer();
		$this->_tpom = $this->getMock("TubePressOptionsManager");
	}
	
	function testGetPlayLink()
	{
		$fakeVideo = new TubePressVideo();
		$fakeVideo->setTitle("fake title");
		$this->_tpom->expects($this->exactly(10))
					->method("get")
					->will($this->returnCallback("greyboxCallback"));
		
		$this->assertEquals(<<<EOT
href="/common/ui/popup.php?id=&amp;opts=r%3D%3Ba%3D%3Bl%3D%3Bg%3D%3Bb%3D%3Bid%3D%3Bw%3D222%3Bh%3D111" title="fake title" rel="gb_page_center[222, 111]"
EOT
			, $this->_sut->getPlayLink($fakeVideo, $this->_tpom));
	}
}

function greyboxCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressEmbeddedOptions::EMBEDDED_HEIGHT => 111,
		TubePressEmbeddedOptions::EMBEDDED_WIDTH => 222,
		TubePressEmbeddedOptions::SHOW_RELATED => false,
    	TubePressEmbeddedOptions::AUTOPLAY => false,
    	TubePressEmbeddedOptions::LOOP => false,
    	TubePressEmbeddedOptions::GENIE => false,
    	TubePressEmbeddedOptions::BORDER => false,
    	TubePressEmbeddedOptions::PLAYER_COLOR => "/"
	);
	return $vals[$args[0]]; 
}