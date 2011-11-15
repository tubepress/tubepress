<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/singlevideotemplate/VideoMeta.class.php';

class org_tubepress_impl_plugin_filters_singlevideotemplate_VideoMetaTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_singlevideotemplate_VideoMeta();
	}

	function testYouTubeFavorites()
	{
	    $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

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

        $video = \Mockery::mock('org_tubepress_api_video_Video');

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::META_SHOULD_SHOW, $shouldShow);
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::META_LABELS, $labels);

        $this->assertEquals($mockTemplate, $this->_sut->alter_singleVideoTemplate($mockTemplate, $video, 'provider-name'));
	}

}

