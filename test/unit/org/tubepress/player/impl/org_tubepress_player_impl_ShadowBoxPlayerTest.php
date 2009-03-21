<?php
class org_tubepress_player_impl_ShadowBoxPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	private $_tpeps;
	private $_ioc;
	
	function setUp()
	{
		global $tubepress_base_url;
		$tubepress_base_url = "fakeurl";
		$this->_sut = new org_tubepress_player_impl_ShadowBoxPlayer();
		$this->_tpeps = $this->getMock("org_tubepress_embedded_EmbeddedPlayerService");
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		$this->_ioc = $this->getMock('org_tubepress_ioc_IocService');
		$this->_sut->setContainer($this->_ioc);
	}
	
	function testGetHeadContents()                                                
    {                          
    	$this->assertEquals(<<<EOX
<script type="text/javascript" src="fakeurl/ui/players/shadowbox/src/js/lib/yui-utilities.js"></script><script type="text/javascript" src="fakeurl/ui/players/shadowbox/src/js/adapter/shadowbox-yui.js"></script><script type="text/javascript" src="fakeurl/ui/players/shadowbox/src/js/shadowbox.js"></script><script type="text/javascript">YAHOO.util.Event.onDOMReady(function() { 
    var options = { assetURL: "fakeurl/ui/players/shadowbox/" };
    Shadowbox.init(options);
});</script><link rel="stylesheet" href="fakeurl/ui/players/shadowbox/src/css/shadowbox.css" type="text/css" />
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
					->will($this->returnCallback("sbCallback"));
		
		$this->_ioc->expects($this->once())
		           ->method('safeGet')
		           ->will($this->returnValue($this->_tpeps));			
					
		$this->_tpeps->expects($this->once())
					 ->method("packOptionsToString")
					 ->will($this->returnValue("fakeopts"));
		
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
		org_tubepress_options_category_Embedded::PLAYER_IMPL => org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE
	);
	return $vals[$args[0]]; 
}
?>