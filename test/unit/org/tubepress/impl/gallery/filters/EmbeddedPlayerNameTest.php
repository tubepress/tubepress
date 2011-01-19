<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/gallery/filters/EmbeddedPlayerName.class.php';

class org_tubepress_impl_gallery_filters_EmbeddedPlayerNameTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		$this->initFakeIoc();
		$this->_sut = new org_tubepress_impl_gallery_filters_EmbeddedPlayerName();
	}
	
	function getMock($className)
	{
	    $mock = parent::getMock($className);

	    if ($className === 'org_tubepress_api_provider_ProviderCalculator') {
	        $mock->expects($this->any())
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
	                 ->with(org_tubepress_api_template_Template::EMBEDDED_IMPL_NAME,
	                     new PHPUnit_Framework_Constraint_IsEqual(org_tubepress_api_embedded_EmbeddedPlayer::LONGTAIL));
	    $this->setOptions(array(org_tubepress_api_const_options_Embedded::PLAYER_IMPL => org_tubepress_api_embedded_EmbeddedPlayer::LONGTAIL));
	    $this->_sut->filter($fakeTemplate);
	}
	
    function testEmbeddedPlayerNameOther()
    {
        $fakeTemplate = $this->getMock('org_tubepress_api_template_Template');
        $fakeTemplate->expects($this->once())
                     ->method('setVariable')
                     ->with(org_tubepress_api_template_Template::EMBEDDED_IMPL_NAME,
                         new PHPUnit_Framework_Constraint_IsEqual('somethingelse'));
        $this->_sut->filter($fakeTemplate);
    }
	
}
?>
