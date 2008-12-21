<?php

class TPGreyBoxPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	private $_tpeps;
	
	function setUp()
	{
		global $tubepress_base_url;
		$tubepress_base_url = "fakeurl";
		$this->_sut = new TPGreyBoxPlayer();
		$this->_tpom = $this->getMock("TubePressOptionsManager");
		$this->_tpeps = $this->getMock("TubePressEmbeddedPlayerService");
	}
	
	function testGetHeadContents()                                                
    {                          
    	$this->assertEquals(<<<EOX
<script type="text/javascript">var GB_ROOT_DIR = "fakeurl/lib/greybox/"</script><script type="text/javascript" src="fakeurl/lib/greybox/AJS.js"></script><script type="text/javascript" src="fakeurl/lib/greybox/AJS_fx.js"></script><script type="text/javascript" src="fakeurl/lib/greybox/gb_scripts.js"></script><link rel="stylesheet" href="fakeurl/lib/greybox/gb_styles.css" type="text/css" />
EOX
		, $this->_sut->getHeadContents());        
    }  
	
	function testGetPlayLink()
	{
		global $tubepress_base_url;
		$fakeVideo = new TubePressVideo();
		$fakeVideo->setTitle("fake title");
		
		$this->_tpeps->expects($this->once())
					 ->method("packOptionsToString")
					 ->will($this->returnValue("fakeopts"));
		
		$this->_sut->setEmbeddedPlayerService($this->_tpeps);
		
		$this->_tpom->expects($this->exactly(2))
					->method("get")
					->will($this->returnCallback("greyboxCallback"));
		
		$this->assertEquals(<<<EOT
href="$tubepress_base_url/common/ui/popup.php?id=&amp;opts=fakeopts" title="fake title" rel="gb_page_center[222, 111]"
EOT
			, $this->_sut->getPlayLink($fakeVideo, $this->_tpom));
	}
}

function greyboxCallback()
{
	$args = func_get_args();
   	$vals = array(
		TubePressEmbeddedOptions::EMBEDDED_HEIGHT => 111,
		TubePressEmbeddedOptions::EMBEDDED_WIDTH => 222
	);
	return $vals[$args[0]]; 
}