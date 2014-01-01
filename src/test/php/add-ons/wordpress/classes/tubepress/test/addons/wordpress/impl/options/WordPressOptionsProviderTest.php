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
class tubepress_test_addons_wordpress_impl_options_WordPressOptionsProviderTest extends tubepress_test_impl_options_AbstractOptionDescriptorProviderTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function prepare(tubepress_spi_options_PluggableOptionDescriptorProvider $sut)
    {
        $this->_mockEnvironmentDetector      = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(true);
    }

    /**
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    protected function getExpectedOptions()
    {
        $toReturn = array();

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_TITLE);
        $option->setDefaultValue('TubePress');
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE);
        $option->setDefaultValue('[tubepress thumbHeight=\'105\' thumbWidth=\'135\']');
        $toReturn[] = $option;

        return $toReturn;
    }

    /**
     * @return tubepress_spi_options_PluggableOptionDescriptorProvider
     */
    protected function buildSut()
    {
        return new tubepress_addons_wordpress_impl_options_WordPressOptionsProvider();
    }
}