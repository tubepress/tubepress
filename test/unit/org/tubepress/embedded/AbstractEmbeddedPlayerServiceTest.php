<?php
abstract class org_tubepress_embedded_impl_AbstractEmbeddedPlayerServiceTest extends PHPUnit_Framework_TestCase {
    
	protected $_sut;
	private $_template;
	
	function parentSetUp($out, $times)
	{
		$this->_sut = $out;
		
		/* Set up options manager */
		$tpom = $this->getMock('org_tubepress_options_manager_OptionsManager');
		$tpom->expects($this->exactly($times))
			 ->method('get')
			 ->will($this->returnCallback('embedded_test_callback'));
	    $this->_sut->setOptionsManager($tpom);
	    
	    /* Set up template */
	    $this->_template = $this->getMock('org_tubepress_template_Template');
	    $this->_template->setPath(__FILE__);
	    $this->_sut->setTemplate($this->_template);
	}
	
    function testToString()
    {
        $this->_template->expects($this->once())
                        ->method('toString');
        $this->_sut->toString('FAKEID');
    }
}

function embedded_test_callback() {
   	$args = func_get_args();
   	$vals = array(
		org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT => '350',
		org_tubepress_options_category_Embedded::EMBEDDED_WIDTH => '450',
		org_tubepress_options_category_Embedded::SHOW_RELATED => true,
		org_tubepress_options_category_Embedded::PLAYER_COLOR => '777777',
		org_tubepress_options_category_Embedded::AUTOPLAY => false,
		org_tubepress_options_category_Embedded::LOOP => true,
		org_tubepress_options_category_Embedded::GENIE => false,
		org_tubepress_options_category_Embedded::BORDER => true,
		org_tubepress_options_category_Embedded::HIGH_QUALITY => true,
		org_tubepress_options_category_Embedded::FULLSCREEN => true,
		org_tubepress_options_category_Embedded::PLAYER_HIGHLIGHT => '111111',
		org_tubepress_options_category_Embedded::SHOW_INFO => false
	);
	return $vals[$args[0]]; 
}
?>