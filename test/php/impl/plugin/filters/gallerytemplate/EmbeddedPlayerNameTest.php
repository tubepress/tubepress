<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/gallerytemplate/EmbeddedPlayerName.class.php';

class org_tubepress_impl_plugin_filters_gallerytemplate_EmbeddedPlayerNameTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_gallerytemplate_EmbeddedPlayerName();

	}

    function testAlterTemplateLongtailYouTube()
    {
        $this->_testCustomYouTube(org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL);
    }

    function testAlterTemplateEmbedPlusYouTube()
    {
        $this->_testCustomYouTube(org_tubepress_api_const_options_values_PlayerImplementationValue::EMBEDPLUS);
    }

    function testAlterTemplateProviderDefault()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn('player-impl');

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME, 'provider-name');

        $this->assertEquals($mockTemplate, $this->_sut->alter_galleryTemplate($mockTemplate, $providerResult, 1, 'provider-name'));
    }

    private function _testCustomYouTube($name)
    {
         $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn($name);

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::EMBEDDED_IMPL_NAME, $name);

        $this->assertEquals($mockTemplate, $this->_sut->alter_galleryTemplate($mockTemplate, $providerResult, 1, org_tubepress_api_provider_Provider::YOUTUBE));

    }
}

