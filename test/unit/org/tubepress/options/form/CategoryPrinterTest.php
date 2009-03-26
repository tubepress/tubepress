<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/form/CategoryPrinter.class.php';

class org_tubepress_options_form_CategoryPrinterTest extends PHPUnit_Framework_TestCase {
    
	private $_stpom;
	private $_msg;
	private $optRef;
	private $sm;
	private $wp;
	
	public function setup()
	{
		$this->_stpom = new org_tubepress_options_form_CategoryPrinter();
        $this->defineMocks();
        $this->applyMocks();
	}

    public function testGetHtmlRegularOptionCategory()
    {
        $this->optRef->expects($this->once())
                     ->method('getOptionNamesForCategory')
                     ->will($this->returnValue(array('f1', 'j2', 'a3', 'r5', 'y3')));  
        $this->assertNotNull($this->_stpom->getHtml('FAKECATEGORYNAME'));
    }
    
    public function testGetHtmlMetaOptionCategory()
    {
        $this->optRef->expects($this->once())
                     ->method('getOptionNamesForCategory')
                     ->will($this->returnValue(array('f1', 'j2', 'a3', 'r5', 'y3')));  
        $this->assertNotNull($this->_stpom->getHtml(org_tubepress_options_Category::META));
    }
    
    public function testGetHtmlGalleryOptionCategory()
    {
        $this->optRef->expects($this->once())
                     ->method('getValidEnumValues')
                     ->will($this->returnValue(array('one', 'bla', 'stuff')));
        $this->assertNotNull($this->_stpom->getHtml(org_tubepress_options_Category::GALLERY));
    }
    
    private function applyMocks()
    {
        $this->_stpom->setMessageService($this->_msg);
        $this->_stpom->setOptionsReference($this->optRef);
        $this->_stpom->setStorageManager($this->sm);
        $this->_stpom->setWidgetPrinter($this->wp);
    }
    
    private function defineMocks()
    {
        $this->_msg = $this->getMock('org_tubepress_message_MessageService');
        $this->optRef = $this->getMock('org_tubepress_options_reference_OptionsReference');
        $this->sm = $this->getMock('org_tubepress_options_storage_StorageManager');
        $this->wp = $this->getMock('org_tubepress_options_form_WidgetPrinter');
    }
}
?>