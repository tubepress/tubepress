<?php

require_once BASE . '/sys/classes/org/tubepress/impl/shortcode/commands/SearchInputCommand.class.php';

class org_tubepress_impl_shortcode_commands_SearchInputCommandTest extends TubePressUnitTest
{
    private $_sut;

    function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_shortcode_commands_SearchInputCommand();
    }

    function testCantExecute()
    {
        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::OUTPUT)->andReturn(org_tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);

        $this->assertFalse($this->_sut->execute(new stdClass()));
    }

    function testExecute()
    {
        $mockChainContext = new stdClass();

        $ioc         = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get('org_tubepress_api_exec_ExecutionContext');
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Output::OUTPUT)->andReturn(org_tubepress_api_const_options_values_OutputValue::SEARCH_INPUT);

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

        $th       = $ioc->get('org_tubepress_api_theme_ThemeHandler');
        $th->shouldReceive('getTemplateInstance')->once()->with('search/search_input.tpl.php')->andReturn($mockTemplate);

        $pm       = $ioc->get('org_tubepress_api_plugin_PluginManager');
        $pm->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::TEMPLATE_SEARCHINPUT, $mockTemplate)->andReturn($mockTemplate);
        $pm->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_SEARCHINPUT, 'template-string')->andReturn('final-value');

        $this->assertTrue($this->_sut->execute($mockChainContext));
        $this->assertEquals('final-value', $mockChainContext->returnValue);

    }
}