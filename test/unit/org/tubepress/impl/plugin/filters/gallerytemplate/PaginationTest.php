<?php

require_once dirname(__FILE__) . '/../../../../../../../../sys/classes/org/tubepress/impl/plugin/filters/gallerytemplate/Pagination.class.php';

class org_tubepress_impl_plugin_filters_gallerytemplate_PaginationTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_gallerytemplate_Pagination();
	}

    public function getMock($className)
    {
        $mock = parent::getMock($className);
        switch ($className) {
           
            case 'org_tubepress_api_plugin_PluginManager':
                $mock->expects($this->once())
                     ->method('runFilters')
                     ->with($this->equalTo(org_tubepress_api_const_plugin_FilterPoint::HTML_PAGINATION), $this->anything())
                     ->will($this->returnCallback(array(new paginationModifier(), 'alter_paginationHtml')));
        }
        return $mock;
    }
	
	function testPaginationAboveAndBelow()
	{
	    $fakeTemplate = $this->getMock('org_tubepress_api_template_Template');
	    $this->_sut->alter_galleryTemplate($fakeTemplate, $this->getMock('org_tubepress_api_provider_ProviderResult'), 3);
	}
	
}

class paginationModifier
{
    public function alter_paginationHtml($point, $html)
    {
        return "<<$html>>";
    }
}