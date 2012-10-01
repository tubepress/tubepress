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
class tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainerTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockOptionDescriptorReference;

    function setUp()
    {
        $this->_sut = new tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer();

        $this->_mockOptionDescriptorReference = Mockery::mock(tubepress_api_service_options_OptionDescriptorReference::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionDescriptorReference);
    }

    function testBuildsNormally()
    {
        $this->assertNotNull($this->_sut);
    }

    function testServiceConstructions()
    {
        $mock1 = new tubepress_api_model_options_OptionDescriptor('mock1');
        $mock1->setBoolean();
        $mock2 = new tubepress_api_model_options_OptionDescriptor('mock2');
        $mock2->setBoolean();

        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with(tubepress_api_const_options_names_OptionsUi::SHOW_VIMEO_OPTIONS)->andReturn($mock1);
        $this->_mockOptionDescriptorReference->shouldReceive('findOneByName')->once()->with(tubepress_api_const_options_names_OptionsUi::SHOW_YOUTUBE_OPTIONS)->andReturn($mock2);

        $toTest = array(

            tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_CONTENT_FILTER         => tubepress_plugins_wordpress_spi_ContentFilter::_,
            tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_MESSAGE                => tubepress_spi_message_MessageService::_,
            tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_OPTIONS_UI_FORMHANDLER => tubepress_spi_options_ui_FormHandler::_,
            tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_CSS_AND_JS_INJECTOR    => tubepress_plugins_wordpress_spi_FrontEndCssAndJsInjector::_,
            tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_OPTIONS_STORAGE        => tubepress_spi_options_StorageManager::_,
            tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_WIDGET_HANDLER         => tubepress_plugins_wordpress_spi_WidgetHandler::_,
            tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_WP_ADMIN_HANDLER       => tubepress_plugins_wordpress_spi_WpAdminHandler::_,
            tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_WP_FUNCTION_WRAPPER    => tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_
        );

        foreach ($toTest as $key => $value) {

            $this->_testServiceBuilt($key, $value);
        }
    }

    private function _testServiceBuilt($id, $class)
    {
        $obj = $this->_sut->get($id);

        $this->assertTrue($obj instanceof $class, "Failed to build $id of type $class. Instead got " . gettype($obj) . var_export($obj, true));
    }


}