<?php
class org_tubepress_player_impl_ShadowBoxPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	private $_tpeps;
	
	function setUp()
	{
		global $tubepress_base_url;
		$tubepress_base_url = "fakeurl";
		$this->_sut = new org_tubepress_player_impl_ShadowBoxPlayer();
		$this->_tpeps = $this->getMock("org_tubepress_video_embed_EmbeddedPlayerService");
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
	}
	
	function testGetHeadContents()                                                
    {                          
    	$this->assertEquals(<<<EOX
<script type="text/javascript" src="fakeurl/lib/shadowbox/src/js/lib/yui-utilities.js"></script><script type="text/javascript" src="fakeurl/lib/shadowbox/src/js/adapter/shadowbox-yui.js"></script><script type="text/javascript" src="fakeurl/lib/shadowbox/src/js/shadowbox.js"></script><script type="text/javascript">YAHOO.util.Event.onDOMReady(function() { 
    var options = { assetURL: "fakeurl/lib/shadowbox/" };
    Shadowbox.init(options);
});</script><link rel="stylesheet" href="fakeurl/lib/shadowbox/src/css/shadowbox.css" type="text/css" />
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
					->will($this->returnCallback("sbCallback"));
		
		$this->_tpeps->expects($this->once())
					 ->method("packOptionsToString")
					 ->will($this->returnValue("fakeopts"));
		
		$this->_sut->setEmbeddedPlayerService($this->_tpeps);
					
		$this->assertEquals(<<<EOT
href="fakeurl/ui/players/popup.php?id=&amp;opts=fakeopts" title="fake title" rel="shadowbox;height=111;width=222"
EOT
			, $this->_sut->getPlayLink($fakeVideo, $this->_tpom));
	}
}

function sbCallback()
{
	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT => 111,
		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH => 222,
	);
	return $vals[$args[0]]; 
}
?>