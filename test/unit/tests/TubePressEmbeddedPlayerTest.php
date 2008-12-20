<?php
include_once dirname(__FILE__) . "/../../../tubepress_classloader.php";

class TubePressEmbeddedPlayerTest extends PHPUnit_Framework_TestCase {
    
	function testAsString()
	{
		$vid = new TubePressVideo();
		$vid->setId("FAKEID");
		
		$this->_tpom = $this->getMock("TubePressOptionsManager");
		
		$this->_tpom->expects($this->exactly(8))
			 ->method("get")
			 ->will($this->returnCallback('callback'));
			 
		$embed = new TubePressEmbeddedPlayer();
		$optionsString = $embed->packOptionsToString($vid, $this->_tpom);
		$embed->parseOptionsFromString($optionsString);
		
		$link = "http://www.youtube.com/v/FAKEID?color1=0x111111&amp;color2=0x777777&amp;rel=1&amp;autoplay=0&amp;loop=1&amp;egm=0&amp;border=1";
		
		$this->assertEquals(<<<EOT
<object type="application/x-shockwave-flash" style="width: 450px; height: 350px" data="$link">
    <param name="wmode" value="transparent" />
    <param name="movie" value="$link" />
</object>
EOT
			,  $embed->toString());
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