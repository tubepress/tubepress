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

	    $messageService = $ioc->get(org_tubepress_api_message_MessageService::_);
	    $messageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
	          return "##$msg##";
	    });

	    $metaNames  = org_tubepress_impl_util_LangUtils::getDefinedConstants(org_tubepress_api_const_options_names_Meta::_);
        $shouldShow = array();
        $labels     = array();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);

	    $odr = $ioc->get(org_tubepress_api_options_OptionDescriptorReference::_);
        $odr->shouldReceive('findOneByName')->times(17)->andReturnUsing(function ($m) {

             $mock = \Mockery::mock(org_tubepress_api_options_OptionDescriptor::_);
             $mock->shouldReceive('getLabel')->once()->andReturn('video-' . $m);
             return $mock;
        });

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

