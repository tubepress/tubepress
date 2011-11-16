<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/gallerytemplate/CoreVariables.class.php';

class org_tubepress_impl_plugin_filters_gallerytemplate_CoreVariablesTest extends TubePressUnitTest
{

    private $_sut;

    public function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_plugin_filters_gallerytemplate_CoreVariables();
    }

    function testAlterTemplate()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::THUMB_WIDTH)->andReturn(556);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Display::THUMB_HEIGHT)->andReturn(984);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn(47);

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
        $providerResult->shouldReceive('getVideoArray')->once()->andReturn('video-array');

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::VIDEO_ARRAY, 'video-array');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::GALLERY_ID, 47);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::THUMBNAIL_WIDTH, 556);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::THUMBNAIL_HEIGHT, 984);

        $this->assertEquals($mockTemplate, $this->_sut->alter_galleryTemplate($mockTemplate, $providerResult, 1, 'provider-name'));
    }
}