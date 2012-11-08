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
class tubepress_plugins_jwplayer_JwPlayerTest extends TubePressUnitTest
{
    private $_mockFieldBuilder;

    private $_mockOptionsDescriptorReference;

    private $_mockEventDispatcher;

    private static $_regexColor = '/^([0-9a-f]{1,2}){3}$/i';

	public function onSetup()
	{
        $this->_mockOptionsDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockFieldBuilder               = $this->createMockSingletonService(tubepress_spi_options_ui_FieldBuilder::_);
        $this->_mockEventDispatcher            = $this->createMockSingletonService('ehough_tickertape_api_IEventDispatcher');
	}

	public function testJwPlayer()
    {
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_BACK);
        $option->setDefaultValue('FFFFFF');
        $option->setLabel('Background color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is FFFFFF');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_FRONT);
        $option->setDefaultValue('000000');
        $option->setLabel('Front color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_LIGHT);
        $option->setDefaultValue('000000');
        $option->setLabel('Light color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwplayer_api_const_options_names_Embedded::COLOR_SCREEN);
        $option->setDefaultValue('000000');
        $option->setLabel('Screen color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $this->_mockEventDispatcher->shouldReceive('addListener')->once()->with(tubepress_api_const_event_CoreEventNames::EMBEDDED_TEMPLATE_CONSTRUCTION,
            Mockery::on(function ($arg) {

                return is_array($arg) && $arg[0] instanceof tubepress_plugins_jwplayer_impl_filters_embeddedtemplate_JwPlayerTemplateVars
                    && $arg[1] === 'onEmbeddedTemplate';
        }));

        require TUBEPRESS_ROOT . '/src/main/php/plugins/jwplayer/JwPlayer.php';

        $this->assertTrue(true);
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