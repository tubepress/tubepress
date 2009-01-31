<?php
class org_tubepress_options_manager_SimpleOptionsManagerTest extends PHPUnit_Framework_TestCase {
    
	private $_stpom;
	
	private $_expectedNames;
	
	public function setup()
	{
		$this->_stpom = new org_tubepress_options_manager_SimpleOptionsManager();
		$this->_expectedNames = array(
			"dateFormat", "debugging_enabled", 
			"filter_racy", "keyword", "randomize_thumbnails", 
			"clientKey", "developerKey", "cacheEnabled", "nofollowLinks",
			"playerLocation", "descriptionLimit", "orderBy",
			"relativeDates", "resultsPerPage", "thumbHeight",
			"thumbWidth", "autoplay", "border",
    	    "embeddedHeight", "embeddedWidth", "genie", "loop",
    		"playerColor", "showRelated", "mode", "favoritesValue",
    		"most_viewedValue", "playlistValue", "tagValue", 
    		"top_ratedValue", "userValue", "widget-title",
    	 	"widget-tagstring", "author", "category", "description",
    	 	"id", "length", "rating", "ratings", "tags", "title",
    	 	"uploaded", "url", "views", "quality", "fullscreen", "embeddableOnly"
    	);
	}
	
    public function testGetAllOptionNamesRightNumberOfOptions()
    {
    	$this->assertEquals(sizeof($this->_expectedNames), sizeof($this->_stpom->getAllOptionNames()));
    }
    
    public function testGetAllOptionsHasRightOptions()
    {
    	$returnedOptions = $this->_stpom->getAllOptionNames();
    	foreach ($this->_expectedNames as $expectedName) {
    		if (!in_array($expectedName, $returnedOptions)) {
    			$this->fail($expectedName . " is missing");
    		}
    	}
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