<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class org_tubepress_impl_shortcode_commands_SearchInputCommandTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockExecutionContext;

    private $_mockThemeHandler;

    private $_mockEventDispatcher;

    function setup()
    {
        $this->_sut = new tubepress_impl_shortcode_commands_SearchInputCommand();

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEventDispatcher  = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_mockThemeHandler     = Mockery::mock(tubepress_spi_theme_ThemeHandler::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setThemeHandler($this->_mockThemeHandler);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
    }

    function testCantExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_RESULTS);

        $this->assertFalse($this->_sut->execute(new ehough_chaingang_impl_StandardContext()));
    }

    function testExecute()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Output::OUTPUT)->andReturn(tubepress_api_const_options_values_OutputValue::SEARCH_INPUT);

        $mockTemplate = Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('toString')->once()->andReturn('template-string');

        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('search/search_input.tpl.php')->andReturn($mockTemplate);

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_event_SearchInputTemplateConstruction::EVENT_NAME)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_SearchInputTemplateConstruction::EVENT_NAME, Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_SearchInputTemplateConstruction && $arg->getSubject() === $mockTemplate;
        }));

        $this->_mockEventDispatcher->shouldReceive('hasListeners')->once()->with(tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_HTML_CONSTRUCTION)->andReturn(true);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_HTML_CONSTRUCTION, Mockery::on(function ($arg) {

            return $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === 'template-string';
        }));

        $context = new ehough_chaingang_impl_StandardContext();

        $this->assertTrue($this->_sut->execute($context));
        $this->assertEquals('template-string', $context->get(tubepress_impl_shortcode_ShortcodeHtmlGeneratorChain::CHAIN_KEY_GENERATED_HTML));

    }
}