<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";

class TPlightWindowPlayerTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	private $_tpom;
	
	function setUp()
	{
		global $tubepress_base_url;
		$tubepress_base_url = "fakeurl";
		$this->_sut = new TPlightWindowPlayer();
		$this->_tpom = $this->getMock("TubePressOptionsManager");
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
		$fakeVideo = new TubePressVideo();
		$fakeVideo->setTitle("fake title");
		$this->_tpom->expects($this->exactly(10))
					->method("get")
					->will($this->returnCallback("lwCallback"));
		
		$this->assertEquals(<<<EOT
href="fakeurl/common/ui/popup.php?id=&amp;opts=r%3D%3Ba%3D%3Bl%3D%3Bg%3D%3Bb%3D%3Bid%3D%3Bw%3D222%3Bh%3D111" class="lightwindow" title="fake title" params="lightwindow_width=222,lightwindow_height=111"
EOT
			, $this->_sut->getPlayLink($fakeVideo, $this->_tpom));
	}
}

function lwCallback()
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
?>