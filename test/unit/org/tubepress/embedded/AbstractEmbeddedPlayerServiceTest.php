<?php
abstract class org_tubepress_embedded_impl_AbstractEmbeddedPlayerServiceTest extends PHPUnit_Framework_TestCase {
    
	protected $_sut;
	
	function parentSetUp($out, $times)
	{
		$this->_sut = $out;
		$tpom = $this->getMock("org_tubepress_options_manager_OptionsManager");
		
		$tpom->expects($this->exactly($times))
			 ->method("get")
			 ->will($this->returnCallback('embedded_test_callback'));
	    $this->_sut->setOptionsManager($tpom);
	}
}

function embedded_test_callback() {
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