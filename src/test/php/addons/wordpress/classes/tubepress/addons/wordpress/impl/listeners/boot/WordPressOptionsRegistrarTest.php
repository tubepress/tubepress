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
class tubepress_addons_wordpress_impl_listeners_boot_WordPressOptionsRegistrarTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsDescriptorReference;

    /**
     * @var tubepress_addons_wordpress_impl_listeners_boot_WordPressOptionsRegistrar
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockOptionsDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_sut                            = new tubepress_addons_wordpress_impl_listeners_boot_WordPressOptionsRegistrar();
    }

    public function testCore()
    {
        $this->_testOptions();

        $this->_sut->onBoot(new ehough_tickertape_Event());

        $this->assertTrue(true);
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