<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/plugin/gallerytemplate/EmbeddedPlayerName.class.php';

class org_tubepress_impl_plugin_gallerytemplate_EmbeddedPlayerNameTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_plugin_gallerytemplate_EmbeddedPlayerName();
	}
	
	function getMock($className)
	{
	    $mock = parent::getMock($className);

	    if ($className === 'org_tubepress_api_provider_ProviderCalculator') {
	        $mock->expects($this->once())
	             ->method('calculateCurrentVideoProvider')
	             ->will($this->returnValue('somethingelse'));
	    }
	    
	    return $mock;
	}

	function testEmbeddedPlayerNameLongtail()
	{
	    $fakeTemplate = $this->getMock('org_tubepress_api_template_Template');
	    $fakeTemplate->expects($this->once())
	                 ->method('setVariable')
	                 ->with(org_tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME,
	                     new PHPUnit_Framework_Constraint_IsEqual(org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL));
	    $this->setOptions(array(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL => org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL));
	    $this->_sut->alter_galleryTemplate($fakeTemplate, $this->getMock('org_tubepress_api_provider_ProviderResult'), 3);
	}
	
    function testEmbeddedPlayerNameOther()
    {
        $fakeTemplate = $this->getMock('org_tubepress_api_template_Template');
        $fakeTemplate->expects($this->once())
                     ->method('setVariable')
                     ->with(org_tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME,
                         new PHPUnit_Framework_Constraint_IsEqual('somethingelse'));
        $this->_sut->alter_galleryTemplate($fakeTemplate, $this->getMock('org_tubepress_api_provider_ProviderResult'), 3);
    }
	
}

