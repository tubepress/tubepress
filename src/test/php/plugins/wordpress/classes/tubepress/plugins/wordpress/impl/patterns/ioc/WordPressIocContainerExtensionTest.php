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
class tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainerExtensionTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_patterns_ioc_IocContainerExtension
     */
    private $_sut;

    /**
     * @var ehough_iconic_impl_ContainerBuilder
     */
    private $_mockParentContainer;

    public function onSetup()
    {
        $this->_sut = new tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainerExtension();

        $this->_mockParentContainer = new ehough_iconic_impl_ContainerBuilder();
    }

    public function testGetAlias()
    {
        $this->assertEquals('wordpress', $this->_sut->getAlias());
    }

    public function testLoad()
    {
        $envDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $envDetector->shouldReceive('isWordPress')->once()->andReturn(true);

        $this->_sut->load($this->_mockParentContainer);

        foreach ($this->_getExpectedServices() as $service) {

            $definition = $this->_mockParentContainer->getDefinition($service->id);

            $this->assertNotNull($definition);

            $this->assertTrue($definition->getClass() === $service->type);

            if (isset($service->tag)) {

                $this->assertTrue($definition->hasTag($service->tag));
            }
        }
    }

    private function _getExpectedServices()
    {
        $map = array(

            array('tubepress_impl_options_ui_tabs_GallerySourceTab', 'tubepress_impl_options_ui_tabs_GallerySourceTab', tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME),
            array('tubepress_impl_options_ui_tabs_ThumbsTab', 'tubepress_impl_options_ui_tabs_ThumbsTab', tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME),
            array('tubepress_impl_options_ui_tabs_EmbeddedTab', 'tubepress_impl_options_ui_tabs_EmbeddedTab', tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME),
            array('tubepress_impl_options_ui_tabs_MetaTab', 'tubepress_impl_options_ui_tabs_MetaTab', tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME),
            array('tubepress_impl_options_ui_tabs_ThemeTab', 'tubepress_impl_options_ui_tabs_ThemeTab', tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME),
            array('tubepress_impl_options_ui_tabs_FeedTab', 'tubepress_impl_options_ui_tabs_FeedTab', tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME),
            array('tubepress_impl_options_ui_tabs_CacheTab', 'tubepress_impl_options_ui_tabs_CacheTab', tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME),
            array('tubepress_impl_options_ui_tabs_AdvancedTab', 'tubepress_impl_options_ui_tabs_AdvancedTab', tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME),
            array(tubepress_spi_message_MessageService::_, 'tubepress_plugins_wordpress_impl_message_WordPressMessageService'),
            array(tubepress_spi_options_StorageManager::_, 'tubepress_plugins_wordpress_impl_options_WordPressStorageManager'),
            array('tubepress_impl_options_ui_DefaultTabsHandler', 'tubepress_impl_options_ui_DefaultTabsHandler'),
            array('tubepress_impl_options_ui_fields_FilterMultiSelectField', 'tubepress_impl_options_ui_fields_FilterMultiSelectField'),
            array(tubepress_spi_options_ui_FormHandler::_, 'tubepress_plugins_wordpress_impl_options_ui_WordPressOptionsFormHandler'),
            array(tubepress_plugins_wordpress_spi_ContentFilter::_, 'tubepress_plugins_wordpress_impl_DefaultContentFilter'),
            array(tubepress_plugins_wordpress_spi_FrontEndCssAndJsInjector::_, 'tubepress_plugins_wordpress_impl_DefaultFrontEndCssAndJsInjector'),
            array(tubepress_plugins_wordpress_spi_WidgetHandler::_, 'tubepress_plugins_wordpress_impl_DefaultWidgetHandler'),
            array(tubepress_plugins_wordpress_spi_WpAdminHandler::_, 'tubepress_plugins_wordpress_impl_DefaultWpAdminHandler'),
            array(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_, 'tubepress_plugins_wordpress_impl_DefaultWordPressFunctionWrapper')
        );

        $toReturn = array();

        foreach ($map as $s) {

            $service = new stdClass();
            $service->id = $s[0];
            $service->type = $s[1];

            if (isset($s[2])) {

                $service->tag = $s[2];
            }

            $toReturn[] = $service;
        }

        return $toReturn;
    }
}