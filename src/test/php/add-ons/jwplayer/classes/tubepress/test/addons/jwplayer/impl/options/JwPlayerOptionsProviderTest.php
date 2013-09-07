<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_jwplayer_impl_options_JwPlayerOptionsProvider
 */
class tubepress_test_addons_jwplayer_impl_listeners_boot_JwPlayerOptionsProviderTest extends tubepress_test_impl_options_AbstractOptionDescriptorProviderTest
{
    private static $_regexColor = '/^([0-9a-f]{1,2}){3}$/i';

    protected function prepare(tubepress_spi_options_PluggableOptionDescriptorProvider $sut)
    {
        $this->_mockOptionsDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
    }

    /**
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    protected function getExpectedOptions()
    {
        $toReturn = array();
        
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK);
        $option->setDefaultValue('FFFFFF');
        $option->setLabel('Background color');
        $option->setDescription('Default is FFFFFF');
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT);
        $option->setDefaultValue('000000');
        $option->setLabel('Front color');
        $option->setDescription('Default is 000000');
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT);
        $option->setDefaultValue('000000');
        $option->setLabel('Light color');
        $option->setDescription('Default is 000000');
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN);
        $option->setDefaultValue('000000');
        $option->setLabel('Screen color');
        $option->setDescription('Default is 000000');
        $option->setValidValueRegex(self::$_regexColor);
        $toReturn[] = $option;

        return $toReturn;
    }

    /**
     * @return tubepress_api_ioc_ContainerExtensionInterface
     */
    protected function buildSut()
    {
        return new tubepress_addons_jwplayer_impl_options_JwPlayerOptionsProvider();
    }
}