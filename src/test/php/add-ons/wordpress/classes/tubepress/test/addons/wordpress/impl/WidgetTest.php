<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_wordpress_impl_Widget
 */
class tubepress_test_addons_wordpress_impl_WidgetTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_Widget
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateBuilder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockShortcodeParser;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockShortCodeHtmlGenerator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_mockMessageService              = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockTemplateBuilder             = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockShortcodeParser             = $this->createMockSingletonService(tubepress_spi_shortcode_ShortcodeParser::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockShortCodeHtmlGenerator      = $this->createMockSingletonService(tubepress_spi_shortcode_ShortcodeHtmlGenerator::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);

        $this->_mockMessageService->shouldReceive('_')->atLeast(1)->andReturnUsing( function ($key) {
            return "<<$key>>";
        });

        $this->_sut = new tubepress_addons_wordpress_impl_Widget();
    }

    public function testPrintWidgetControl()
    {
        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_addons_wordpress_impl_Widget::WIDGET_CONTROL_TITLE, '<<Title>>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_addons_wordpress_impl_Widget::WIDGET_TITLE, 'value of widget title');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_addons_wordpress_impl_Widget::WIDGET_CONTROL_SHORTCODE, '<<TubePress shortcode for the widget. See the <a href="http://docs.tubepress.com/" target="_blank">documentation</a>.>>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_addons_wordpress_impl_Widget::WIDGET_SHORTCODE, 'value of widget shortcode');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_addons_wordpress_impl_Widget::WIDGET_SUBMIT_TAG, tubepress_addons_wordpress_impl_Widget::WIDGET_SUBMIT_TAG);
        $mockTemplate->shouldReceive('toString')->once()->andReturn('final result');

        $this->_mockStorageManager->shouldReceive('fetch')->once()->with(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE)->andReturn('value of widget title');
        $this->_mockStorageManager->shouldReceive('fetch')->once()->with(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE)->andReturn('value of widget shortcode');
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress-widget-submit')->andReturn(false);
        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/src/main/php/add-ons/wordpress/resources/templates/widget_controls.tpl.php')->andReturn($mockTemplate);

        ob_start();

        $this->_sut->printControlHtml();

        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('final result', $contents);
    }

    public function testPrintWidget()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE)->andReturn('shortcode string');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('theme');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE)->andReturn('widget title');
        $this->_mockExecutionContext->shouldReceive('getCustomOptions')->once()->andReturn(array(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH => 22135));
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with(array(
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE    => 3,
            tubepress_api_const_options_names_Meta::VIEWS                  => false,
            tubepress_api_const_options_names_Meta::DESCRIPTION            => true,
            tubepress_api_const_options_names_Meta::DESC_LIMIT          => 50,
            tubepress_api_const_options_names_Embedded::PLAYER_LOCATION => 'popup',
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

    public function testWidgetErrorCondition()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE)->andReturn('shortcode string');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::THEME)->andReturn('theme');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE)->andReturn('widget title');
        $this->_mockExecutionContext->shouldReceive('getCustomOptions')->once()->andReturn(array(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH => 22135));
        $this->_mockExecutionContext->shouldReceive('setCustomOptions')->once()->with(array(
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE    => 3,
            tubepress_api_const_options_names_Meta::VIEWS                  => false,
            tubepress_api_const_options_names_Meta::DESCRIPTION            => true,
            tubepress_api_const_options_names_Meta::DESC_LIMIT          => 50,
            tubepress_api_const_options_names_Embedded::PLAYER_LOCATION => 'popup',
            tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT        => 105,
            tubepress_api_const_options_names_Thumbs::THUMB_WIDTH         => 22135,
            tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE      => false,
            tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW      => false,
            tubepress_api_const_options_names_Thumbs::THEME               => 'sidebar',
            tubepress_api_const_options_names_Thumbs::FLUID_THUMBS        => false
        ));
        $this->_mockExecutionContext->shouldReceive('reset')->once();

        $this->_mockShortCodeHtmlGenerator->shouldReceive('getHtmlForShortcode')->once()->with('')->andThrow(new Exception('crazy stuff happened'));

        $this->_mockShortcodeParser->shouldReceive('parse')->once()->with('shortcode string');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::ERROR_EXCEPTION_CAUGHT, ehough_mockery_Mockery::on(function ($event) {

            return $event instanceof tubepress_api_event_EventInterface && $event->getArgument('message') === 'crazy stuff happened'
            && $event->getSubject() instanceof Exception;
        }));

        ob_start();
        $this->_sut->printWidgetHtml(array(
            'before_widget' => 'before_widget',
            'before_title'  => 'before_title',
            'after_title'   => 'after_title',
            'after_widget'  => 'after_widget'
        ));
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('before_widgetbefore_titlewidget titleafter_titlecrazy stuff happenedafter_widget', $contents);
    }
}