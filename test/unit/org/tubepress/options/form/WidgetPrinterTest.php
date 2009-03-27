<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/form/WidgetPrinter.class.php';

class org_tubepress_options_form_WidgetPrinterTest extends PHPUnit_Framework_TestCase {

    private $_msg;
    private $optRef;
    private $sm;
	private $_stpom;
	
	public function setup()
	{
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
	    $this->assertEquals(<<<EOT

<input type="text" name="test" size="20" value="bla" />

EOT
	    , $this->_stpom->getHtml('test'));		    
	}

	public function testGetHtmlColor()
	{
	    $this->optRef->expects($this->once())
	                 ->method('getType')
	                 ->will($this->returnValue(org_tubepress_options_Type::COLOR));
	    $this->sm->expects($this->once())
	                 ->method('get')
	                 ->will($this->returnValue('stuff'));
	    $this->assertEquals(<<<EOT

<input type="text" name="test" size="6" class="color" value="stuff" />

EOT
	    , $this->_stpom->getHtml('test'));	    
	}	
	
	public function testGetHtmlCheckbox()
	{
	    $this->optRef->expects($this->once())
	                 ->method('getType')
	                 ->will($this->returnValue(org_tubepress_options_Type::BOOL));
	    $this->sm->expects($this->once())
	                 ->method('get')
	                 ->will($this->returnValue(true));
	    $this->assertEquals(<<<EOT

<input type="checkbox" name="test" value="test" CHECKED />

EOT
	    , $this->_stpom->getHtml('test'));	    
	}
	
    public function testGetHtmlMenu()
	{
	    $this->optRef->expects($this->once())
	                 ->method('getType')
	                 ->will($this->returnValue(org_tubepress_options_Type::ORDER));
	    $this->sm->expects($this->once())
	                 ->method('get')
	                 ->will($this->returnValue('ss'));
	    $this->assertEquals(<<<EOT

<input type="checkbox" name="test" value="test" CHECKED />

EOT
	    , $this->_stpom->getHtml('test'));	    
	}
	
	public function testGetHtmlForRadio()
	{
	    $this->assertEquals(<<<EOT

<input type="radio" name="mode" id="test" value="test"  />

EOT
	    , $this->_stpom->getHtmlForRadio('test'));
	}
	
    private function applyMocks()
    {
        $this->_stpom->setMessageService($this->_msg);
        $this->_stpom->setOptionsReference($this->optRef);
        $this->_stpom->setStorageManager($this->sm);
    }
    
    private function defineMocks()
    {
        $this->_msg = $this->getMock('org_tubepress_message_MessageService');
        $this->optRef = $this->getMock('org_tubepress_options_reference_OptionsReference');
        $this->sm = $this->getMock('org_tubepress_options_storage_StorageManager');
    }
}
?>