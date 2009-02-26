<?php

class org_tubepress_player_impl_GreyBoxPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	private $_tpeps;
	private $_ioc;
	
	function setUp()
	{
		global $tubepress_base_url;
		$tubepress_base_url = "fakeurl";
		$this->_sut = new org_tubepress_player_impl_GreyBoxPlayer();
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_tpeps = $this->getMock("org_tubepress_embedded_EmbeddedPlayerService");
		$this->_ioc = $this->getMock('org_tubepress_ioc_IocService');
		$this->_sut->setContainer($this->_ioc);
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
		
		$this->_ioc->expects($this->once())
		           ->method('safeGet')
		           ->will($this->returnValue($this->_tpeps)); 			 
					 
		$this->_tpom->expects($this->exactly(3))
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
		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH => 222,
		org_tubepress_options_category_Embedded::PLAYER_IMPL => org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE
	);
	return $vals[$args[0]]; 
}