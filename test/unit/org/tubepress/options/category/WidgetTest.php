<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Widget.class.php';

class org_tubepress_options_category_WidgetTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_options_category_Widget();
	}
	
	function testPrintForOptionsForm()
	{
		
	}
}
?>