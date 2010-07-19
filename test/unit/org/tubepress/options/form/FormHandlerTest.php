<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/form/FormHandler.class.php';

class org_tubepress_options_form_FormHandlerTest extends TubePressUnitTest {
    
    private $_stpom;
	
	public function setup()
	{
		$this->_stpom = new org_tubepress_options_form_FormHandler();
		$this->_stpom->setIocService($this->getIoc());
	}
	
	public function testDisplay()
	{
        $this->_stpom->getHtml();
	}
	
	public function testCollect()
	{
	    $fakePostVars = array('test', 'two', 'poo');
	    $this->assertNull($this->_stpom->collect($fakePostVars));
	}
	
	private function expected()
	{
	    return file_get_contents(dirname(__FILE__) . '/expected.txt');
	}
}
?>