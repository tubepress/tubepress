<?php
class org_tubepress_player_impl_LightWindowPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	private $_tpeps;
	private $_ioc;
	
	function setUp()
	{
		global $tubepress_base_url;
		$tubepress_base_url = "fakeurl";
		$this->_sut = new org_tubepress_player_impl_LightWindowPlayer();
		$this->_tpeps = $this->getMock("org_tubepress_embedded_EmbeddedPlayerService");
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
	    $this->_ioc = $this->getMock('org_tubepress_ioc_IocService');
		$this->_sut->setContainer($this->_ioc);
	}
	
	function testGetHeadContents()                                                
    {                          
    	$this->assertEquals(<<<EOX
<script type="text/javascript" src="fakeurl/ui/players/lightWindow/javascript/prototype.js"></script><script type="text/javascript" src="fakeurl/ui/players/lightWindow/javascript/scriptaculous.js?load=effects"></script><script type="text/javascript" src="fakeurl/ui/players/lightWindow/javascript/lightWindow.js"></script><script type="text/javascript">var tubepressLWPath = "fakeurl/ui/players/lightWindow/"</script><link rel="stylesheet" href="fakeurl/ui/players/lightWindow/css/lightWindow.css" type="text/css" />
EOX
		, $this->_sut->getHeadContents());        
    }  
	
	function testGetPlayLink()
	{
		global $tubepress_base_url;
		$fakeVideo = new org_tubepress_video_Video();
		$fakeVideo->setTitle("fake title");
		$this->_tpom->expects($this->exactly(3))
					->method("get")
					->will($this->returnCallback("lwCallback"));
		
		$this->_tpeps->expects($this->once())
					 ->method("packOptionsToString")
					 ->will($this->returnValue("fakeopts"));
					 
	    $this->_ioc->expects($this->once())
		           ->method('safeGet')
		           ->will($this->returnValue($this->_tpeps)); 
		
		$this->assertEquals(<<<EOT
href="fakeurl/ui/players/popup.php?id=&amp;opts=fakeopts" class="lightwindow" title="fake title" params="lightwindow_width=222,lightwindow_height=111"
EOT
			, $this->_sut->getPlayLink($fakeVideo, $this->_tpom));
	}
}

function lwCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT => 111,
		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH => 222,
		org_tubepress_options_category_Embedded::PLAYER_IMPL => org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE
	);
	return $vals[$args[0]]; 
}
?>