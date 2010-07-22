<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Embedded.class.php';


class org_tubepress_options_category_EmbeddedTest extends PHPUnit_Framework_TestCase {
    
	private $_expectedNames;
	private $_actualNames;
	private $_sut;
	
	public function setup()
	{
		$this->_expectedNames = array(
			'playerImplementation', 'autoplay', 'border', 'embeddedHeight', 'embeddedWidth', 'hd',
			'genie', 'loop', 'playerColor', 'playerHighlight', 'showRelated',
		    'fullscreen', 'showInfo'
    	);
    	$class = new ReflectionClass('org_tubepress_options_category_Embedded');    
        $this->_actualNames = $class->getConstants();
        $this->_sut = new org_tubepress_options_category_Embedded();
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