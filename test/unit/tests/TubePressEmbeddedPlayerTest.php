<?php
class TubePressEmbeddedPlayerTest extends PHPUnit_Framework_TestCase {
    
	function testAsString()
	{
		$vid = new TubePressVideo();
		$vid->setId("FAKEID");
		
		$this->_tpom = $this->getMock("TubePressOptionsManager");
		
		$this->_tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback('callback'));
			 
		$embed = new TubePressEmbeddedPlayer($vid, $this->_tpom);

		$link = "http://www.youtube.com/v/FAKEID&amp;color1=0x111111&amp;color2=0x777777&amp;rel=1&amp;autoplay=0&amp;loop=1&amp;egm=0&amp;border=1";
		
		$this->assertEquals('<object type="application/x-shockwave-flash" style="width:450px;height:350px" data="' . $link . '"><param name="wmode" value="transparent" /><param name="movie" value="' . $link . '" /></object>', $embed->toString());
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
		TubePressEmbeddedOptions::BORDER => true
	);
	return $vals[$args[0]]; 
}
?>