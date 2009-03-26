<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/form/FormHandler.class.php';

class org_tubepress_options_form_FormHandlerTest extends PHPUnit_Framework_TestCase {
    
	private $_stpom;
	
	public function setup()
	{
		$this->_stpom = new org_tubepress_options_form_FormHandler();
	}
	
    
}
?>