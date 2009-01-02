<?php
class SimpleTubePressEmbeddedPlayerServiceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new SimpleTubePressEmbeddedPlayerService();
	}
	
	function testToString()
	{
		$vid = new TubePressVideo();
		$vid->setId("FAKEID");
		
		$this->_tpom = $this->getMock("TubePressOptionsManager");
		
		$this->_tpom->expects($this->exactly(9))
			 ->method("get")
			 ->will($this->returnCallback('callback'));
			 
		$this->_sut->applyOptions($vid, $this->_tpom);
		
		$link = "http://www.youtube.com/v/FAKEID?color1=0x111111&amp;color2=0x777777&amp;rel=1&amp;autoplay=0&amp;loop=1&amp;egm=0&amp;border=1";
		
		$this->assertEquals(<<<EOT
<object type="application/x-shockwave-flash" style="width: 450px; height: 350px" data="$link">
    <param name="wmode" value="transparent" />
    <param name="movie" value="$link" />
</object>
EOT
			,  $this->_sut->toString());
	}
}

function callback() {
   	$args = func_get_args();
   	$vals = array(
		TubePressEmbeddedOptions::EMBEDDED_HEIGHT => "350",
		TubePressEmbeddedOptions::EMBEDDED_WIDTH => "450",
		TubePressEmbeddedOptions::SHOW_RELATED => true,
		TubePressEmbeddedOptions::PLAYER_COLOR => "0x111111/0x777777",
		TubePressEmbeddedOptions::AUTOPLAY => false,
		TubePressEmbeddedOptions::LOOP => true,
		TubePressEmbeddedOptions::GENIE => false,
		TubePressEmbeddedOptions::BORDER => true,
		TubePressEmbeddedOptions::QUALITY => "normal"
	);
	return $vals[$args[0]]; 
}
?>