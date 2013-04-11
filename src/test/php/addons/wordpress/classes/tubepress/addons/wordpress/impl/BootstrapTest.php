<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_wordpress_impl_BootstrapTest extends TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsDescriptorReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContentFilter;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockJsAndCssInjector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWpAdminHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWidgetHandler;

    public function onSetup()
    {
        $this->_mockEnvironmentDetector        = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockOptionsDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockWordPressFunctionWrapper   = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);
        $this->_mockContentFilter              = $this->createMockSingletonService(tubepress_addons_wordpress_spi_ContentFilter::_);
        $this->_mockJsAndCssInjector           = $this->createMockSingletonService(tubepress_addons_wordpress_spi_FrontEndCssAndJsInjector::_);
        $this->_mockWpAdminHandler             = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpAdminHandler::_);
        $this->_mockWidgetHandler              = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WidgetHandler::_);
    }

    public function testCore()
    {
        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(true);

        $this->_testOptions();
        $this->_testAdmin();
        $this->_testApi();

        require TUBEPRESS_ROOT . '/src/main/php/addons/wordpress/scripts/bootstrap.php';

        global $tubepress_base_url;

        $this->assertEquals('valueofcontenturl/addons/tubepress', $tubepress_base_url);
    }

    private function _testApi()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_version')->once()->andReturn('3.1');

        $this->_mockWordPressFunctionWrapper->shouldReceive('content_url')->once()->andReturn('valueofcontenturl');

        $this->_mockWordPressFunctionWrapper->shouldReceive('load_plugin_textdomain')->once()->with('tubepress', false, 'tubepress/src/main/resources/i18n');

        $this->_mockWordPressFunctionWrapper->shouldReceive('add_filter')->once()->with('the_content', array($this->_mockContentFilter, 'filterContent'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('wp_head', array($this->_mockJsAndCssInjector, 'printInHtmlHead'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('init', array($this->_mockJsAndCssInjector, 'registerStylesAndScripts'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('admin_menu', array($this->_mockWpAdminHandler, 'registerAdminMenuItem'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('admin_enqueue_scripts', array($this->_mockWpAdminHandler, 'registerStylesAndScripts'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('widgets_init', array($this->_mockWidgetHandler, 'registerWidget'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_filter')->once()->with('plugin_row_meta', array($this->_mockWpAdminHandler, 'modifyMetaRowLinks'), 10, 2);
    }
    
    private function _testAdmin()
    {
        //$this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(true);
    }

    private function _testOptions()
    {
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE);
        $option->setDefaultValue('TubePress');
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE);
        $option->setDefaultValue('[tubepress thumbHeight=\'105\' thumbWidth=\'135\']');
        $this->_verifyOption($option);
    }

    private function _verifyOption(tubepress_spi_options_OptionDescriptor $expectedOption)
    {
        $this->_mockOptionsDescriptorReference->shouldReceive('registerOptionDescriptor')->once()->with(ehough_mockery_Mockery::on(function ($registeredOption) use ($expectedOption) {

            return $registeredOption instanceof tubepress_spi_options_OptionDescriptor
                && $registeredOption->getAcceptableValues() === $expectedOption->getAcceptableValues()
                && $registeredOption->getAliases() === $expectedOption->getAliases()
                && $registeredOption->getDefaultValue() === $expectedOption->getDefaultValue()
                && $registeredOption->getDescription() === $expectedOption->getDescription()
                && $registeredOption->getLabel() === $expectedOption->getLabel()
                && $registeredOption->getName() === $expectedOption->getName()
                && $registeredOption->getValidValueRegex() === $expectedOption->getValidValueRegex()
                && $registeredOption->isAbleToBeSetViaShortcode() === $expectedOption->isAbleToBeSetViaShortcode()
                && $registeredOption->isBoolean() === $expectedOption->isBoolean()
                && $registeredOption->isMeantToBePersisted() === $expectedOption->isMeantToBePersisted()
                && $registeredOption->hasDiscreteAcceptableValues() === $expectedOption->hasDiscreteAcceptableValues()
                && $registeredOption->isProOnly() === $expectedOption->isProOnly();
        }));
    }
}