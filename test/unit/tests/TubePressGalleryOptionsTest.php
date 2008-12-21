<?php
class TubePressGalleryOptionsTest extends PHPUnit_Framework_TestCase {
    
    private $_expectedNames;
	private $_actualNames;
	private $_tpsm;
	private $_template;	
	private $_sut;
	private $_msgService;
	
	public function setup()
	{
		$this->_expectedNames = array(
			"mode", "favoritesValue", "most_viewedValue", "playlistValue", 
			"tagValue", "top_ratedValue", "userValue"
    	);
    	$class = new ReflectionClass("TubePressGalleryOptions");    
        $this->_actualNames = $class->getConstants();
        $this->_tpsm = $this->getMock("TubePressStorageManager");
        $this->_template = $this->getMock("HTML_Template_IT");
        $this->_sut = new TubePressGalleryOptions();
        $this->_msgService = $this->getMock("TubePressMessageService");
	}

	public function testPrintForOptionsPage()
	{
		$this->_sut->setMessageService($this->_msgService);
		$this->_sut->printForOptionsForm($this->_template, $this->_tpsm);
	}
	
	public function testHasRightOptionNames()
	{
		foreach ($this->_expectedNames as $expectedName) {
			$this->assertTrue(in_array($expectedName, $this->_actualNames));
		}
	}
	
	public function testHasRightNumberOfOptions()
	{
		$this->assertEquals(sizeof($this->_expectedNames), sizeof($this->_actualNames));
	}    
	
}
?>