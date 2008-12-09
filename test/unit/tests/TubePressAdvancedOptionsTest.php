<?php
class TubePressAdvancedOptionsTest extends PHPUnit_Framework_TestCase {
	
	private $_expectedNames;
	
	private $_actualNames;
	
	public function setup()
	{
		$this->_expectedNames = array(
			"dateFormat", "debugging_enabled", 
			"filter_racy", "keyword", "randomize_thumbnails", 
			"clientKey", "developerKey", "cacheEnabled", "nofollowLinks"
    	);
    	$class = new ReflectionClass("TubePressAdvancedOptions");    
        $this->_actualNames = $class->getConstants();
	}
	
	public function testHasRightOptionNames()
	{
		foreach ($this->_expectedNames as $expectedName) {
			if (!in_array($expectedName, $this->_actualNames)) {
				$this->fail($expectedName . " is missing");
			}
		}
	}
	
	public function testHasRightNumberOfOptions()
	{
		$this->assertEquals(sizeof($this->_expectedNames), sizeof($this->_actualNames));
	}
	
}
?>