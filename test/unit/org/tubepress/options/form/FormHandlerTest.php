<?php

class org_tubepress_options_form_FormHandlerTest extends PHPUnit_Framework_TestCase {
    
    private $_msg;
    private $optRef;
    private $sm;
    private $_stpom;
    private $_template;
	
	public function setup()
	{
	    require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/form/FormHandler.class.php';
		$this->_stpom = new org_tubepress_options_form_FormHandler();
		$this->defineMocks();
        $this->applyMocks();
	}
	
	public function testDisplay()
	{
	    $this->optRef->expects($this->once())
                     ->method('getOptionCategoryNames')
                     ->will($this->returnValue(array('f1', 'j2', 'a3', 'r5', 'y3')));  
        $this->assertNull($this->_stpom->display());
	}
	
	public function testCollect()
	{
	    $this->optRef->expects($this->once())
                     ->method('getAllOptionNames')
                     ->will($this->returnValue(array('f1', 'j2', 'a3', 'r5', 'y3')));  
	    $fakePostVars = array('test', 'two', 'poo');
	    $this->assertNull($this->_stpom->collect($fakePostVars));
	}
	
    private function applyMocks()
    {
        $this->_stpom->setMessageService($this->_msg);
        $this->_stpom->setOptionsReference($this->optRef);
        $this->_stpom->setStorageManager($this->sm);
        $this->_stpom->setTemplate($this->_template);
    }
    
    private function defineMocks()
    {
        $this->_msg = $this->getMock('org_tubepress_message_MessageService');
        $this->optRef = $this->getMock('org_tubepress_options_reference_OptionsReference');
        $this->sm = $this->getMock('org_tubepress_options_storage_StorageManager');
        $this->_template = $this->getMock('org_tubepress_template_Template');
    }
}
?>