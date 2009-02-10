<?php
class org_tubepress_video_embed_SimpleEmbeddedPlayerServiceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_video_embed_SimpleEmbeddedPlayerService();
	}
	
	function testToString()
	{
		$vid = new org_tubepress_video_Video();
		$vid->setId("FAKEID");
		
		$this->_tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		
		$this->_tpom->expects($this->exactly(12))
			 ->method("get")
			 ->will($this->returnCallback('callback'));
			 
		$this->_sut->applyOptions($vid, $this->_tpom);
		
		$link = "http://www.youtube.com/v/FAKEID&amp;color2=0x777777&amp;color1=0x111111&amp;rel=1&amp;autoplay=0&amp;loop=1&amp;egm=0&amp;border=1&amp;fs=1&amp;showinfo=0";
		
		$this->assertEquals(<<<EOT
<object type="application/x-shockwave-flash" 
    style="width: 450px; height: 350px" data="$link">
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
		org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT => "350",
		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH => "450",
		org_tubepress_options_category_Embedded::SHOW_RELATED => true,
		org_tubepress_options_category_Embedded::PLAYER_COLOR => "777777",
		org_tubepress_options_category_Embedded::AUTOPLAY => false,
		org_tubepress_options_category_Embedded::LOOP => true,
		org_tubepress_options_category_Embedded::GENIE => false,
		org_tubepress_options_category_Embedded::BORDER => true,
		org_tubepress_options_category_Embedded::QUALITY => "normal",
		org_tubepress_options_category_Embedded::FULLSCREEN => true,
		org_tubepress_options_category_Embedded::PLAYER_HIGHLIGHT => "111111",
		org_tubepress_options_category_Embedded::SHOW_INFO => false
		
	);
	return $vals[$args[0]]; 
}
?>