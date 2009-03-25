<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Gallery.class.php';

class org_tubepress_options_category_GalleryTest extends PHPUnit_Framework_TestCase {
    
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
    	$class = new ReflectionClass("org_tubepress_options_category_Gallery");    
        $this->_actualNames = $class->getConstants();
        $this->_tpsm = $this->getMock("org_tubepress_options_storage_StorageManager");
        $this->_template = $this->getMock("net_php_pear_HTML_Template_IT");
        $this->_sut = new org_tubepress_options_category_Gallery();
        $this->_msgService = $this->getMock("org_tubepress_message_MessageService");
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