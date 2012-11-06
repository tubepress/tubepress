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
class tubepress_impl_env_wordpress_WordPressFormHandlerTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockTabs;

    private $_mockFilter;

    private $_mockMessageService;

    private $_mockTemplateBuilder;

    function setUp()
    {
        $this->_mockTabs = Mockery::mock(tubepress_spi_options_ui_FormHandler::_);
        $this->_mockFilter = Mockery::mock('tubepress_spi_options_ui_Field');

        $this->_mockMessageService   = Mockery::mock(tubepress_spi_message_MessageService::_);
        $this->_mockTemplateBuilder = Mockery::mock('ehough_contemplate_api_TemplateBuilder');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($this->_mockMessageService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setTemplateBuilder($this->_mockTemplateBuilder);

        $this->_sut = new tubepress_plugins_wordpress_impl_options_ui_WordPressOptionsFormHandler($this->_mockTabs, $this->_mockFilter);
    }


    function testGetFailureMessagesOneError()
    {
        $this->_mockTabs->shouldReceive('onSubmit')->once()->andReturn(array('holy smokes!'));
        $this->_mockFilter->shouldReceive('onSubmit')->once()->andReturn(null);

        $this->assertEquals(array('holy smokes!'), $this->_sut->onSubmit());
    }

    function testOnSubmit()
    {


        $this->_mockTabs->shouldReceive('onSubmit')->once()->andReturn(null);
        $this->_mockFilter->shouldReceive('onSubmit')->once()->andReturn(null);

        $this->assertNull($this->_sut->onSubmit());
    }

    function testGetHtml()
    {
        $template       = \Mockery::mock('ehough_contemplate_api_Template');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/src/main/php/plugins/wordpress/resources/templates/options_page.tpl.php')->andReturn($template);

        $this->_mockMessageService->shouldReceive('_')->once()->with('TubePress Options')->andReturn('<<title>>');
        $this->_mockMessageService->shouldReceive('_')->once()->with('Here you can set the default options for TubePress. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.org/documentation">documentation</a> for more information.')->andReturn('<<blurb>>');
        $this->_mockMessageService->shouldReceive('_')->once()->with('Save')->andReturn('<<save>>');

        $this->_mockTabs->shouldReceive('getHtml')->once()->andReturn('<<tabhtml>>');

        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TITLE, '<<title>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_INTRO, '<<blurb>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_TEXT, '<<save>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_SAVE_ID, 'tubepress_save');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_TABS, '<<tabhtml>>');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_AbstractFormHandler::TEMPLATE_VAR_FILTER, $this->_mockFilter);
        $template->shouldReceive('toString')->once()->andReturn('foo');

        $this->assertEquals('foo', $this->_sut->getHtml());
    }
}