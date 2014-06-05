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
 * @covers tubepress_wordpress_impl_Widget
 */
class tubepress_test_wordpress_impl_WidgetTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_Widget
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
    private $_mockStringUtils;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWpFunctions;

    public function onSetup()
    {
        $this->_mockMessageService              = $this->mock(tubepress_core_translation_api_TranslatorInterface::_);
        $this->_mockExecutionContext            = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockEnvironmentDetector         = $this->mock(tubepress_core_environment_api_EnvironmentInterface::_);
        $this->_mockTemplateBuilder             = $this->mock(tubepress_core_template_api_TemplateFactoryInterface::_);
        $this->_mockShortcodeParser             = $this->mock(tubepress_core_shortcode_api_ParserInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_http_api_RequestParametersInterface::_);
        $this->_mockShortCodeHtmlGenerator      = $this->mock(tubepress_core_html_api_HtmlGeneratorInterface::_);
        $this->_mockStorageManager              = $this->mock(tubepress_core_options_api_PersistenceInterface::_);
        $this->_mockWpFunctions                 = $this->mock(tubepress_wordpress_spi_WpFunctionsInterface::_);
        $this->_mockStringUtils                 = $this->mock(tubepress_api_util_StringUtilsInterface::_);

        $this->_mockMessageService->shouldReceive('_')->atLeast(1)->andReturnUsing( function ($key) {
            return "<<$key>>";
        });

        $this->_sut = new tubepress_wordpress_impl_Widget(
            $this->_mockExecutionContext,
            $this->_mockStorageManager,
            $this->_mockMessageService,
            $this->_mockShortCodeHtmlGenerator,
            $this->_mockShortcodeParser,
            $this->_mockWpFunctions,
            $this->_mockStringUtils,
            $this->_mockHttpRequestParameterService,
            $this->_mockTemplateBuilder
        );
    }

    public function testPrintWidgetControl()
    {
        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_wordpress_impl_Widget::WIDGET_CONTROL_TITLE, '<<Title>>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_wordpress_impl_Widget::WIDGET_TITLE, 'value of widget title');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_wordpress_impl_Widget::WIDGET_CONTROL_SHORTCODE, '<<TubePress shortcode for the widget. See the <a href="http://docs.tubepress.com/" target="_blank">documentation</a>.>>');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_wordpress_impl_Widget::WIDGET_SHORTCODE, 'value of widget shortcode');
        $mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_wordpress_impl_Widget::WIDGET_SUBMIT_TAG, tubepress_wordpress_impl_Widget::WIDGET_SUBMIT_TAG);
        $mockTemplate->shouldReceive('toString')->once()->andReturn('final result');

        $this->_mockStorageManager->shouldReceive('fetch')->once()->with(tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE)->andReturn('value of widget title');
        $this->_mockStorageManager->shouldReceive('fetch')->once()->with(tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE)->andReturn('value of widget shortcode');
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress-widget-submit')->andReturn(false);
        $this->_mockTemplateBuilder->shouldReceive('fromFilesystem')->once()->with(array(TUBEPRESS_ROOT . '/src/main/add-ons/wordpress/resources/templates/widget_controls.tpl.php'))->andReturn($mockTemplate);

        ob_start();

        $this->_sut->printControlHtml();

        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('final result', $contents);
    }

    public function testPrintWidget()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_wordpress_api_Constants::OPTION_WIDGET_SHORTCODE)->andReturn('shortcode string');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_theme_api_Constants::OPTION_THEME)->andReturn('theme');
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_wordpress_api_Constants::OPTION_WIDGET_TITLE)->andReturn('widget title');
        $this->_mockExecutionContext->shouldReceive('getEphemeralOptions')->once()->andReturn(array(tubepress_core_html_gallery_api_Constants::OPTION_THUMB_WIDTH => 22135));
        $this->_mockExecutionContext->shouldReceive('setEphemeralOptions')->once()->with(array(
            tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE    => 3,
            tubepress_core_media_item_api_Constants::OPTION_VIEWS                  => false,
            tubepress_core_media_item_api_Constants::OPTION_DESCRIPTION            => true,
            tubepress_core_media_item_api_Constants::OPTION_DESC_LIMIT          => 50,
            tubepress_core_player_api_Constants::OPTION_PLAYER_LOCATION => 'shadowbox',
            tubepress_core_html_gallery_api_Constants::OPTION_THUMB_HEIGHT        => 105,
            tubepress_core_html_gallery_api_Constants::OPTION_THUMB_WIDTH         => 22135,
            tubepress_core_html_gallery_api_Constants::OPTION_PAGINATE_ABOVE      => false,
            tubepress_core_html_gallery_api_Constants::OPTION_PAGINATE_BELOW      => false,
            tubepress_core_theme_api_Constants::OPTION_THEME               => 'tubepress/legacy-sidebar',
            tubepress_core_html_gallery_api_Constants::OPTION_FLUID_THUMBS        => false
        ));
        $this->_mockExecutionContext->shouldReceive('setEphemeralOptions')->once()->with(array());

        $this->_mockShortCodeHtmlGenerator->shouldReceive('getHtmlForShortcode')->once()->with('')->andReturn('html result');

        $this->_mockShortcodeParser->shouldReceive('parse')->once()->with('syz');

        $this->_mockStringUtils->shouldReceive('removeNewLines')->once()->with('shortcode string')->andReturn('syz');

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
}