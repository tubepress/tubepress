<?php

require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/options/SimpleOptionsManager.class.php';

class org_tubepress_impl_options_SimpleOptionsManagerTest extends TubePressUnitTest {
    
	private $_sut;
	
	private $_expectedNames;
	
	public function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_options_SimpleOptionsManager();
	}
    
	public function testSetGet()
	{
		$this->_sut->set(org_tubepress_api_const_options_names_Display::THEME, 'crazytheme');
		$this->assertEquals('crazytheme', $this->_sut->get(org_tubepress_api_const_options_names_Display::THEME));
	}

    public function testGetSetShortcode()
    {
    	$this->_sut->setShortcode("fakeshort");
    	$this->assertEquals("fakeshort", $this->_sut->getShortcode());
    }
    
    public function testGetCustomOption()
    {
	$customOptions = array(org_tubepress_api_const_options_names_Display::THEME => 'fakeoptionvalue');
    	$this->_sut->setCustomOptions($customOptions);
    	$this->assertEquals('fakeoptionvalue', $this->_sut->get(org_tubepress_api_const_options_names_Display::THEME));
	$this->assertEquals(1, sizeof(array_intersect($customOptions, $this->_sut->getCustomOptions())));
    }
    
    public function testGetCustomOptionFallback()
    {
    	$this->_sut->get("nonexistent");
    }
}
?>
