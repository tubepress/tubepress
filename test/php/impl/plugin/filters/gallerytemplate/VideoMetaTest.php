<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/gallerytemplate/VideoMeta.class.php';

class org_tubepress_impl_plugin_filters_gallerytemplate_VideoMetaTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_gallerytemplate_VideoMeta();
	}

	function testVideoMetaAboveAndBelow()
	{
	    $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

	    $messageService = $ioc->get('org_tubepress_api_message_MessageService');
	    $messageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
	          return "##$msg##";
	    });

	    $metaNames  = org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_CategoryName::META);
        $shouldShow = array();
        $labels     = array();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

        foreach ($metaNames as $metaName) {
            $shouldShow[$metaName] = "<<value of $metaName>>";
            $labels[$metaName]     = '##video-' . $metaName . '##';

            $execContext->shouldReceive('get')->once()->with($metaName)->andReturnUsing(function ($m) {
                   return "<<value of $m>>";
            });
        }

	    $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
	    $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::META_LABELS, $labels);

        $providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');

	    $this->assertEquals($mockTemplate, $this->_sut->alter_galleryTemplate($mockTemplate, $providerResult, 1, org_tubepress_api_provider_Provider::YOUTUBE));
	}

}

