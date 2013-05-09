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
class tubepress_addons_jwplayer_impl_listeners_boot_JwPlayerOptionsRegistrarTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsDescriptorReference;

    /**
     * @var tubepress_addons_jwplayer_impl_listeners_boot_JwPlayerOptionsRegistrar
     */
    private $_sut;

    private static $_regexColor = '/^([0-9a-f]{1,2}){3}$/i';

    public function onSetup()
    {
        $this->_mockOptionsDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);

        $this->_sut = new tubepress_addons_jwplayer_impl_listeners_boot_JwPlayerOptionsRegistrar();
    }

    public function testJwPlayer()
    {
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_BACK);
        $option->setDefaultValue('FFFFFF');
        $option->setLabel('Background color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is FFFFFF');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_FRONT);
        $option->setDefaultValue('000000');
        $option->setLabel('Front color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT);
        $option->setDefaultValue('000000');
        $option->setLabel('Light color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_addons_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN);
        $option->setDefaultValue('000000');
        $option->setLabel('Screen color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $this->_sut->onBoot(new tubepress_spi_event_EventBase());

        $this->assertTrue(true);
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