<?php
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/category/Gallery.class.php';

class org_tubepress_options_category_GalleryTest extends PHPUnit_Framework_TestCase {
    
    private $_expectedNames;
	private $_actualNames;
	private $_sut;
	
	public function setup()
	{
		$this->_expectedNames = array(
			'mode', 'favoritesValue', 'most_viewedValue', 'playlistValue', 
			'tagValue', 'top_ratedValue', 'userValue', 'video', 'vimeoUploadedByValue',
			'vimeoLikesValue', 'vimeoAppearsInValue', 'vimeoSearchValue', 'vimeoCreditedToValue',
			'vimeoChannelValue', 'vimeoAlbumValue', 'vimeoGroupValue', 'directoryValue'
    	);
    	$class = new ReflectionClass('org_tubepress_options_category_Gallery');    
        $this->_actualNames = $class->getConstants();
        $this->_sut = new org_tubepress_options_category_Gallery();
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