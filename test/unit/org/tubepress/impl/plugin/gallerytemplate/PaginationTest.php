<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/plugin/gallerytemplate/Pagination.class.php';

class org_tubepress_impl_plugin_gallerytemplate_PaginationTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_plugin_gallerytemplate_Pagination();
	}
	
	function getMock($className)
	{
	    $mock = parent::getMock($className);

	    
	    return $mock;
	}

	function testPaginationAboveAndBelow()
	{
	    $fakeTemplate = $this->getMock('org_tubepress_api_template_Template');
	    $this->_sut->alter_galleryTemplate($fakeTemplate, $this->getMock('org_tubepress_api_provider_ProviderResult'), 3);
	}
	
}
?>
