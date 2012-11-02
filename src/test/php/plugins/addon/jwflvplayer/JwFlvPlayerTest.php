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
class tubepress_plugins_jwflvplayer_JwFlvPlayerTest extends TubePressUnitTest
{
    private $_mockServiceCollectionsRegistry;

    private $_mockFieldBuilder;

    private $_mockOptionsDescriptorReference;

    private $_mockEventDispatcher;

    private static $_regexColor = '/^([0-9a-f]{1,2}){3}$/i';

	public function setup()
	{
        $this->_mockServiceCollectionsRegistry = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);
        $this->_mockOptionsDescriptorReference = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockFieldBuilder               = Mockery::mock(tubepress_spi_options_ui_FieldBuilder::_);
        $this->_mockEventDispatcher            = Mockery::mock('ehough_tickertape_api_IEventDispatcher');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockServiceCollectionsRegistry);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionsDescriptorReference);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionsUiFieldBuilder($this->_mockFieldBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
	}

	public function testJwFlvPlayer()
    {
        $this->_mockServiceCollectionsRegistry->shouldReceive('registerService')->once()->with(

            tubepress_spi_embedded_PluggableEmbeddedPlayerService::_,
            Mockery::type('tubepress_plugins_jwflvplayer_impl_embedded_JwFlvPluggableEmbeddedPlayerService'));

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwflvplayer_api_const_options_names_Embedded::COLOR_BACK);
        $option->setDefaultValue('FFFFFF');
        $option->setLabel('JW FLV Player background color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is FFFFFF');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwflvplayer_api_const_options_names_Embedded::COLOR_FRONT);
        $option->setDefaultValue('000000');
        $option->setLabel('JW FLV Player front color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwflvplayer_api_const_options_names_Embedded::COLOR_LIGHT);
        $option->setDefaultValue('000000');
        $option->setLabel('JW FLV Player light color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_jwflvplayer_api_const_options_names_Embedded::COLOR_SCREEN);
        $option->setDefaultValue('000000');
        $option->setLabel('JW FLV Player screen color');           //>(translatable)<                                                                                                                                                                                                                                 //>(translatable)<
        $option->setDescription('Default is 000000');                  //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->_verifyOption($option);

        $mockColorBackField = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
        $mockColorFrontField = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
        $mockColorLightField = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
        $mockColorScreenField = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);

        $this->_mockFieldBuilder->shouldReceive('build')->once()->with(

            tubepress_plugins_jwflvplayer_api_const_options_names_Embedded::COLOR_BACK,
            tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME,
            'embedded'
        )->andReturn($mockColorBackField);

        $this->_mockFieldBuilder->shouldReceive('build')->once()->with(

            tubepress_plugins_jwflvplayer_api_const_options_names_Embedded::COLOR_FRONT,
            tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME,
            'embedded'
        )->andReturn($mockColorFrontField);

        $this->_mockFieldBuilder->shouldReceive('build')->once()->with(

            tubepress_plugins_jwflvplayer_api_const_options_names_Embedded::COLOR_LIGHT,
            tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME,
            'embedded'
        )->andReturn($mockColorLightField);

        $this->_mockFieldBuilder->shouldReceive('build')->once()->with(

            tubepress_plugins_jwflvplayer_api_const_options_names_Embedded::COLOR_SCREEN,
            tubepress_impl_options_ui_fields_ColorField::FIELD_CLASS_NAME,
            'embedded'
        )->andReturn($mockColorScreenField);

        $this->_mockServiceCollectionsRegistry->shouldReceive('registerService')->once()->with(tubepress_spi_options_ui_Field::CLASS_NAME, $mockColorBackField);
        $this->_mockServiceCollectionsRegistry->shouldReceive('registerService')->once()->with(tubepress_spi_options_ui_Field::CLASS_NAME, $mockColorFrontField);
        $this->_mockServiceCollectionsRegistry->shouldReceive('registerService')->once()->with(tubepress_spi_options_ui_Field::CLASS_NAME, $mockColorLightField);
        $this->_mockServiceCollectionsRegistry->shouldReceive('registerService')->once()->with(tubepress_spi_options_ui_Field::CLASS_NAME, $mockColorScreenField);

        $this->_mockEventDispatcher->shouldReceive('addListener')->once()->with(tubepress_api_const_event_CoreEventNames::EMBEDDED_TEMPLATE_CONSTRUCTION,
            Mockery::on(function ($arg) {

                return is_array($arg) && $arg[0] instanceof tubepress_plugins_jwflvplayer_impl_filters_embeddedtemplate_JwFlvTemplateVars
                    && $arg[1] === 'onEmbeddedTemplate';
        }));

        require TUBEPRESS_ROOT . '/src/main/php/plugins/addon/jwflvplayer/JwFlvPlayer.php';

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