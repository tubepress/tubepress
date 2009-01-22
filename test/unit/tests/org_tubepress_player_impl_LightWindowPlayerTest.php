<?php
class org_tubepress_player_impl_LightWindowPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	private $_tpeps;
	
	function setUp()
	{
		global $tubepress_base_url;
		$tubepress_base_url = "fakeurl";
		$this->_sut = new org_tubepress_player_impl_LightWindowPlayer();
		$this->_tpeps = $this->getMock("org_tubepress_video_embed_EmbeddedPlayerService");
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
	}
	
	function testGetHeadContents()                                                
    {                          
    	$this->assertEquals(<<<EOX
<script type="text/javascript">var tubepressLWPath = "fakeurl/lib/lightWindow/"</script><script type="text/javascript" src="fakeurl/lib/lightWindow/javascript/prototype.js"></script><script type="text/javascript" src="fakeurl/lib/lightWindow/javascript/scriptaculous.js?load=effects"></script><script type="text/javascript" src="fakeurl/lib/lightWindow/javascript/lightWindow.js"></script><link rel="stylesheet" href="fakeurl/lib/lightWindow/css/lightWindow.css" type="text/css" />
EOX
		, $this->_sut->getHeadContents());        
    }  
	
	function testGetPlayLink()
	{
		global $tubepress_base_url;
		$fakeVideo = new org_tubepress_video_Video();
		$fakeVideo->setTitle("fake title");
		$this->_tpom->expects($this->exactly(2))
					->method("get")
					->will($this->returnCallback("lwCallback"));
		
		$this->_tpeps->expects($this->once())
					 ->method("packOptionsToString")
					 ->will($this->returnValue("fakeopts"));
		
		$this->_sut->setEmbeddedPlayerService($this->_tpeps);
					
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
	);
	return $vals[$args[0]]; 
}
?>