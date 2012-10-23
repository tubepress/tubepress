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
class tubepress_plugins_wordpress_WordPressTest extends TubePressUnitTest
{
    private $_mockEnvironmentDetector;

    private $_mockOptionsDescriptorReference;

    private $_mockServiceCollectionsRegistry;

    private $_mockWordPressFunctionWrapper;

    private $_mockContentFilter;

    private $_mockJsAndCssInjector;

    private $_mockWpAdminHandler;

    private $_mockWidgetHandler;

    function setup()
    {
        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockOptionsDescriptorReference = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockServiceCollectionsRegistry = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);
        $this->_mockWordPressFunctionWrapper   = Mockery::mock(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);
        $this->_mockContentFilter       = Mockery::mock(tubepress_plugins_wordpress_spi_ContentFilter::_);
        $this->_mockJsAndCssInjector    = Mockery::mock(tubepress_plugins_wordpress_spi_FrontEndCssAndJsInjector::_);
        $this->_mockWpAdminHandler      = Mockery::mock(tubepress_plugins_wordpress_spi_WpAdminHandler::_);
        $this->_mockWidgetHandler       = Mockery::mock(tubepress_plugins_wordpress_spi_WidgetHandler::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionsDescriptorReference);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockServiceCollectionsRegistry);
        tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::setWordPressFunctionWrapper($this->_mockWordPressFunctionWrapper);
        tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::setContentFilter($this->_mockContentFilter);
        tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::setFrontEndCssAndJsInjector($this->_mockJsAndCssInjector);
        tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::setWpAdminHandler($this->_mockWpAdminHandler);
        tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::setWidgetHandler($this->_mockWidgetHandler);
    }

    function testCore()
    {

        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(true);

        $this->_testOptions();
        $this->_testAdmin();
        $this->_testApi();

        require __DIR__ . '/../../../../../main/php/plugins/addon/wordpress/WordPress.php';

        global $tubepress_base_url;
        $this->assertEquals('valueofcontenturl/plugins/tubepress', $tubepress_base_url);
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
        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(true);

        $this->_mockServiceCollectionsRegistry->shouldReceive('registerService')->times(8)->with(

            tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME,
            Mockery::on(function ($arg) {
                return $arg instanceof  tubepress_impl_options_ui_tabs_GallerySourceTab ||
                    $arg instanceof  tubepress_impl_options_ui_tabs_ThumbsTab ||
                    $arg instanceof  tubepress_impl_options_ui_tabs_EmbeddedTab ||
                    $arg instanceof  tubepress_impl_options_ui_tabs_MetaTab ||
                    $arg instanceof  tubepress_impl_options_ui_tabs_ThemeTab ||
                    $arg instanceof  tubepress_impl_options_ui_tabs_FeedTab ||
                    $arg instanceof  tubepress_impl_options_ui_tabs_CacheTab ||
                    $arg instanceof  tubepress_impl_options_ui_tabs_AdvancedTab;
            })
        );
    }

    private function _testOptions()
    {
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_wordpress_api_const_options_names_WordPress::WIDGET_TITLE);
        $option->setDefaultValue('TubePress');
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE);
        $option->setDefaultValue('[tubepress thumbHeight=\'105\' thumbWidth=\'135\']');
        $this->_verifyOption($option);
    }

    private function _verifyOption(tubepress_spi_options_OptionDescriptor $expectedOption)
    {
        $this->_mockOptionsDescriptorReference->shouldReceive('registerOptionDescriptor')->once()->with(Mockery::on(function ($registeredOption) use ($expectedOption) {

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