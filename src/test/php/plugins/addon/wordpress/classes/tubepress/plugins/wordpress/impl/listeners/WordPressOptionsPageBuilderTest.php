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
class tubepress_plugins_wordpress_impl_listeners_WordPressOptionsPageBuilderTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_wordpress_impl_listeners_WordPressOptionsPageBuilder
     */
    private $_sut;

    private $_mockServiceCollectionsRegistry;

    private $_mockWordPressFunctionWrapper;

    public function setUp()
    {
        $this->_sut = new tubepress_plugins_wordpress_impl_listeners_WordPressOptionsPageBuilder();

        $this->_mockServiceCollectionsRegistry = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);
        $this->_mockWordPressFunctionWrapper   = Mockery::mock(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockServiceCollectionsRegistry);
        tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::setWordPressFunctionWrapper($this->_mockWordPressFunctionWrapper);
    }

    public function testAdmin()
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

        $this->_sut->onBoot(new tubepress_api_event_TubePressEvent());

        $this->assertTrue(true);
    }

    public function testNotAdmin()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);

        $this->_sut->onBoot(new tubepress_api_event_TubePressEvent());

        $this->assertTrue(true);
    }

}