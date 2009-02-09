<?php
class org_tubepress_options_manager_SimpleOptionsManagerTest extends PHPUnit_Framework_TestCase {
    
	private $_stpom;
	
	private $_expectedNames;
	
	public function setup()
	{
		$this->_stpom = new org_tubepress_options_manager_SimpleOptionsManager();
		
	}
	
    
    public function testGetSetShortcode()
    {
    	$this->_stpom->setShortcode("fakeshort");
    	$this->assertEquals("fakeshort", $this->_stpom->getShortcode());
    }
    
    public function testGetCustomOption()
    {
    	$this->_stpom->setCustomOptions(array("fakeoptionname" => "fakeoptionvalue"));
    	$this->assertEquals("fakeoptionvalue", $this->_stpom->get("fakeoptionname"));
    }
    
    public function testGetCustomOptionFallback()
    {
    	$tpsm = $this->getMock("org_tubepress_options_storage_StorageManager");
    	$tpsm->expects($this->any())
    		 ->method("get")
    		 ->with($this->equalTo("nonexistent"));
    	$this->_stpom->setStorageManager($tpsm);
    	$this->_stpom->get("nonexistent");
    }
    
    public function testSetorg_tubepress_options_manager_OptionsManager()
    {
    	$tpsm = $this->getMock("org_tubepress_options_storage_StorageManager");
    	$this->_stpom->setStorageManager($tpsm);
    }
}
?>