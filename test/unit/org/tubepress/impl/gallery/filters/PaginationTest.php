<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/gallery/filters/Pagination.class.php';

class org_tubepress_impl_gallery_filters_PaginationTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_gallery_filters_Pagination();
	}
	
	function getMock($className)
	{
	    $mock = parent::getMock($className);

	    
	    return $mock;
	}

	function testPaginationAboveAndBelow()
	{
	    $fakeTemplate = $this->getMock('org_tubepress_api_template_Template');
	    $this->_sut->filter($fakeTemplate, $this->getMock('org_tubepress_api_feed_FeedResult'), 3);
	}
	
}
?>
