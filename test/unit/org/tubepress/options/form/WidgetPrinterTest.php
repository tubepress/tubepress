<?php

class org_tubepress_options_form_WidgetPrinterTest extends PHPUnit_Framework_TestCase {

    private $_msg;
    private $optRef;
    private $sm;
    private $_tpl;
	private $_stpom;
	
	public function setup()
	{
	    require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/form/WidgetPrinter.class.php';
		$this->_stpom = new org_tubepress_options_form_WidgetPrinter();
		$this->defineMocks();
        $this->applyMocks();
	}
	
	public function testGetHtmlText()
	{
	    $this->optRef->expects($this->once())
	                 ->method('getType')
	                 ->will($this->returnValue(org_tubepress_options_Type::TEXT));
	    $this->sm->expects($this->once())
	                 ->method('get')
	                 ->will($this->returnValue('bla'));
	    $this->_tpl->expects($this->once())
	               ->method('getHtml')
	               ->will($this->returnValue('fake html'));
	    $this->assertEquals('fake html' , $this->_stpom->getHtml('test'));		    
	}

	public function testGetHtmlColor()
	{
	    $this->optRef->expects($this->once())
	                 ->method('getType')
	                 ->will($this->returnValue(org_tubepress_options_Type::COLOR));
	    $this->sm->expects($this->once())
	                 ->method('get')
	                 ->will($this->returnValue('stuff'));
	    $this->_tpl->expects($this->once())
	               ->method('getHtml')
	               ->will($this->returnValue('foobar'));
	    $this->assertEquals('foobar', $this->_stpom->getHtml('test'));	    
	}	
	
	public function testGetHtmlCheckbox()
	{
	    $this->optRef->expects($this->once())
	                 ->method('getType')
	                 ->will($this->returnValue(org_tubepress_options_Type::BOOL));
	    $this->sm->expects($this->once())
	                 ->method('get')
	                 ->will($this->returnValue(true));
        $this->_tpl->expects($this->once())
                   ->method('getHtml')
                   ->will($this->returnValue('foobar'));
	    $this->assertEquals('foobar', $this->_stpom->getHtml('test'));	    
	}
	
    public function testGetHtmlMenu()
	{
	    $this->optRef->expects($this->once())
	                 ->method('getType')
	                 ->will($this->returnValue(org_tubepress_options_Type::ORDER));
	    $this->optRef->expects($this->once())
	                 ->method('getValidEnumValues')
	                 ->will($this->returnValue(array('bla', 'rre', 'stuff')));
	    $this->_msg->expects($this->any())
	               ->method('_')
	               ->will($this->returnCallback('messageCallback'));
	    $this->sm->expects($this->once())
	                 ->method('get')
	                 ->will($this->returnValue('ss'));
        $this->_tpl->expects($this->once())
                   ->method('getHtml')
                   ->will($this->returnValue('foobar'));
	    $this->assertEquals('foobar', $this->_stpom->getHtml('test'));	    
	}
	
	public function testGetHtmlForRadio()
	{
        $this->_tpl->expects($this->once())
                   ->method('getHtml')
                   ->will($this->returnValue('foobar')); 
	    $this->assertEquals('foobar', $this->_stpom->getHtmlForRadio('test'));
	}
	
    private function applyMocks()
    {
        $this->_stpom->setMessageService($this->_msg);
        $this->_stpom->setOptionsReference($this->optRef);
        $this->_stpom->setStorageManager($this->sm);
        $this->_stpom->setTemplate($this->_tpl);
    }
    
    private function defineMocks()
    {
        $this->_msg = $this->getMock('org_tubepress_message_MessageService');
        $this->optRef = $this->getMock('org_tubepress_options_reference_OptionsReference');
        $this->sm = $this->getMock('org_tubepress_options_storage_StorageManager');
        $this->_tpl = $this->getMock('org_tubepress_template_Template');
    }
}

function messageCallback($arg) {
    return "Message $arg";
}
?>