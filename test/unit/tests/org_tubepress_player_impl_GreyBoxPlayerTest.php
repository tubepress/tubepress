<?php

class org_tubepress_player_impl_GreyBoxPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	private $_tpeps;
	
	function setUp()
	{
		global $tubepress_base_url;
		$tubepress_base_url = "fakeurl";
		$this->_sut = new org_tubepress_player_impl_GreyBoxPlayer();
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_tpeps = $this->getMock("org_tubepress_video_embed_EmbeddedPlayerService");
	}
	
	function testGetHeadContents()                                                
    {                          
    	$this->assertEquals(<<<EOX
<script type="text/javascript" src="fakeurl/ui/players/greybox/AJS.js"></script><script type="text/javascript" src="fakeurl/ui/players/greybox/AJS_fx.js"></script><script type="text/javascript" src="fakeurl/ui/players/greybox/gb_scripts.js"></script><script type="text/javascript">var GB_ROOT_DIR = "fakeurl/ui/players/greybox/"</script><link rel="stylesheet" href="fakeurl/ui/players/greybox/gb_styles.css" type="text/css" />
EOX
		, $this->_sut->getHeadContents());        
    }  
	
	function testGetPlayLink()
	{
		global $tubepress_base_url;
		$fakeVideo = new org_tubepress_video_Video();
		$fakeVideo->setTitle("fake title");
		
		$this->_tpeps->expects($this->once())
					 ->method("packOptionsToString")
					 ->will($this->returnValue("fakeopts"));
		
		$this->_sut->setEmbeddedPlayerService($this->_tpeps);
		
		$this->_tpom->expects($this->exactly(2))
					->method("get")
					->will($this->returnCallback("greyboxCallback"));
		
		$this->assertEquals(<<<EOT
href="$tubepress_base_url/ui/players/popup.php?id=&amp;opts=fakeopts" title="fake title" rel="gb_page_center[222, 111]"
EOT
			, $this->_sut->getPlayLink($fakeVideo, $this->_tpom));
	}
}

function greyboxCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT => 111,
		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH => 222
	);
	return $vals[$args[0]]; 
}