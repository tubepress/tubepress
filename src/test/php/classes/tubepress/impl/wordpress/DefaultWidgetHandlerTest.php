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
class org_tubepress_impl_env_wordpress_WidgetTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockMessageService;

    private $_mockExecutionContext;

    private $_mockEnvironmentDetector;

    private $_mockTemplateBuilder;

    private $_mockHttpRequestParameterService;

    private $_mockShortcodeParser;

    private $_mockShortCodeHtmlGenerator;

    private $_mockStorageManager;

    private $_mockWpFunctionWrapper;

    function setUp()
    {
        $this->_sut = new tubepress_impl_wordpress_DefaultWidgetHandler();

        $this->_mockMessageService   = Mockery::mock(tubepress_spi_message_MessageService::_);
        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockTemplateBuilder = Mockery::mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockShortcodeParser = Mockery::mock(tubepress_spi_shortcode_ShortcodeParser::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockShortCodeHtmlGenerator = Mockery::mock(tubepress_spi_shortcode_ShortcodeHtmlGenerator::_);
        $this->_mockStorageManager  = Mockery::mock(tubepress_spi_options_StorageManager::_);
        $this->_mockWpFunctionWrapper = Mockery::mock(tubepress_spi_wordpress_WordPressFunctionWrapper::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($this->_mockMessageService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setTemplateBuilder($this->_mockTemplateBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setShortcodeHtmlParser($this->_mockShortcodeParser);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setShortcodeHtmlGenerator($this->_mockShortCodeHtmlGenerator);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionStorageManager($this->_mockStorageManager);
        tubepress_impl_wordpress_WordPressServiceLocator::setWordPressFunctionWrapper($this->_mockWpFunctionWrapper);

        $this->_mockMessageService->shouldReceive('_')->atLeast(1)->andReturnUsing( function ($key) {
            return "<<$key>>";
        });
    }

    function testPrintWidgetControl()
    {
        $mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_CONTROL_TITLE, '<<Title>>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_TITLE, 'value of widget title');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_CONTROL_SHORTCODE, '<<TubePress shortcode for the widget. See the <a href="http://tubepress.org/documentation"> documentation</a>.>>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_SHORTCODE, 'value of widget shortcode');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_SUBMIT_TAG, tubepress_impl_wordpress_DefaultWidgetHandler::WIDGET_SUBMIT_TAG);
        $mockTemplate->shouldReceive('toString')->once()->andReturn('final result');

        $this->_mockStorageManager->shouldReceive('get')->once()->with(tubepress_api_const_options_names_WordPress::WIDGET_TITLE)->andReturn('value of widget title');
        $this->_mockStorageManager->shouldReceive('get')->once()->with(tubepress_api_const_options_names_WordPress::WIDGET_SHORTCODE)->andReturn('value of widget shortcode');
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress-widget-submit')->andReturn(false);
        $this->_mockEnvironmentDetector->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('fakepath');
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('fakepath/sys/ui/templates/wordpress/widget_controls.tpl.php')->andReturn($mockTemplate);

        ob_start();

        $this->_sut->printControlHtml();

        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('final result', $contents);
    }

    function testPrintWidget()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_WordPress::WIDGET_SHORTCODE)->andReturn('shortcode string');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('theme');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_WordPress::WIDGET_TITLE)->andReturn('widget title');
        $this->_mockExecutionContext->shouldReceive('getCustomOptions')->once()->andReturn(array(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH => 22135));
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with(array(
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE    => 3,
            tubepress_api_const_options_names_Meta::VIEWS                  => false,
            tubepress_api_const_options_names_Meta::DESCRIPTION            => true,
            tubepress_api_const_options_names_Meta::DESC_LIMIT          => 50,
            tubepress_api_const_options_names_Embedded::PLAYER_LOCATION => tubepress_api_const_options_values_PlayerLocationValue::POPUP,
            tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT        => 105,
            tubepress_api_const_options_names_Thumbs::THUMB_WIDTH         => 22135,
            tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE      => false,
            tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW      => false,
            tubepress_api_const_options_names_Thumbs::THEME               => 'sidebar',
            tubepress_api_const_options_names_Thumbs::FLUID_THUMBS        => false
        ));
        $this->_mockExecutionContext->shouldReceive('reset')->once();

        $this->_mockShortCodeHtmlGenerator->shouldReceive('getHtmlForShortcode')->once()->with('')->andReturn('html result');

        $this->_mockShortcodeParser->shouldReceive('parse')->once()->with('shortcode string');

        ob_start();
        $this->_sut->printWidgetHtml(array(
    		'before_widget' => 'before_widget',
    		'before_title'  => 'before_title',
    		'after_title'   => 'after_title',
    		'after_widget'  => 'after_widget'
        ));
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('before_widgetbefore_titlewidget titleafter_titlehtml resultafter_widget', $contents);
    }

    function testInitAction()
    {
        $widgetOps = array('classname' => 'widget_tubepress', 'description' => '<<Displays YouTube or Vimeo videos with TubePress>>');

        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_sidebar_widget')->once()->with('tubepress', 'TubePress', array($this->_sut, 'printWidgetHtml'), $widgetOps);
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_widget_control')->once()->with('tubepress', 'TubePress', array($this->_sut, 'printControlHtml'));


        $this->_sut->registerWidget();

        $this->assertTrue(true);
    }
}
