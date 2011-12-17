<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/searchinputtemplate/CoreVariables.class.php';

class org_tubepress_impl_plugin_filters_searchinputtemplate_CoreVariablesTest extends TubePressUnitTest
{
	private $_sut;

	function setup()
	{
		parent::setUp();
		$this->_sut = new org_tubepress_impl_plugin_filters_searchinputtemplate_CoreVariables();
	}

	function testYouTubeFavorites()
	{
	    $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $context      = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $context->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL)->andReturn('');

        $qss = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
        $qss->shouldReceive('getFullUrl')->once()->andReturn('http://tubepress.org?foo=bar&something=else');
        $qss->shouldReceive('getSearchTerms')->once()->andReturn('search for something');

        $ms         = $ioc->get(org_tubepress_api_message_MessageService::_);
        $ms->shouldReceive('_')->once()->andReturnUsing(function ($msg) {
            return "##$msg##";
        });

        $video = \Mockery::mock('org_tubepress_api_video_Video');

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::SEARCH_HANDLER_URL, 'http://tubepress.org?foo=bar&something=else');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::SEARCH_HIDDEN_INPUTS, array('foo' => 'bar', 'something' => 'else'));
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::SEARCH_TERMS, 'search for something');
        $mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::SEARCH_BUTTON, '##Search##');

        $this->assertEquals($mockTemplate, $this->_sut->alter_searchInputTemplate($mockTemplate));
	}

}

