<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Display.class.php';

class org_tubepress_options_category_DisplayTest extends PHPUnit_Framework_TestCase {
    
    private $_expectedNames;
	private $_actualNames;
	private $_sut;
	
	public function setup()
	{
		$this->_expectedNames = array(
			'ajaxPagination', 'playerLocation', 'descriptionLimit', 'orderBy', 'relativeDates', 
			'resultsPerPage', 'thumbHeight', 'thumbWidth', 'paginationAbove', 'paginationBelow',
			'hqThumbs', 'randomize_thumbnails', 'theme'
    	);
    	$class = new ReflectionClass('org_tubepress_options_category_Display');    
        $this->_actualNames = $class->getConstants();
        $this->_sut = new org_tubepress_options_category_Display();
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