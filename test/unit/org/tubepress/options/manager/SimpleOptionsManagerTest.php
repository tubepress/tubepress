<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/manager/SimpleOptionsManager.class.php';

class org_tubepress_options_manager_SimpleOptionsManagerTest extends TubePressUnitTest {
    
	private $_sut;
	
	private $_expectedNames;
	
	public function setup()
	{
	    $ioc = $this->getIoc();
		$this->_sut = new org_tubepress_options_manager_SimpleOptionsManager();
		$this->_sut->setContainer($ioc);
	}
    
    public function testGetSetShortcode()
    {
    	$this->_sut->setShortcode("fakeshort");
    	$this->assertEquals("fakeshort", $this->_sut->getShortcode());
    }
    
    public function testGetCustomOption()
    {
    	$this->_sut->setCustomOptions(array(org_tubepress_options_category_Display::THEME => 'fakeoptionvalue'));
    	$this->assertEquals('fakeoptionvalue', $this->_sut->get(org_tubepress_options_category_Display::THEME));
    }
    
    public function testGetCustomOptionFallback()
    {
    	$this->_sut->get("nonexistent");
    }
}
?>