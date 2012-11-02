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
class tubepress_plugins_vimeo_VimeoTest extends TubePressUnitTest
{
    private $_mockVideoProviderRegistry;

    private $_mockOptionsDescriptorReference;

    private $_mockFieldBuilder;

    private static $_regexWordChars = '/\w+/';
    private static $_regexColor     = '/^([0-9a-f]{1,2}){3}$/i';

	function setup()
	{
        $this->_mockVideoProviderRegistry = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);
        $this->_mockOptionsDescriptorReference = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockFieldBuilder = Mockery::mock(tubepress_spi_options_ui_FieldBuilder::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionsDescriptorReference);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockVideoProviderRegistry);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionsUiFieldBuilder($this->_mockFieldBuilder);
	}

	function testVimeo()
    {
        $this->_mockVideoProviderRegistry->shouldReceive('registerService')->once()->with(

            tubepress_spi_embedded_PluggableEmbeddedPlayerService::_,
            Mockery::type('tubepress_plugins_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService'));

        $this->_mockVideoProviderRegistry->shouldReceive('registerService')->once()->with(
            tubepress_spi_provider_PluggableVideoProviderService::_,
            Mockery::type('tubepress_plugins_vimeo_impl_provider_VimeoPluggableVideoProviderService'));

        $this->_testOptions();

        $this->_testOptionsPageUi();

        require __DIR__ . '/../../../../../main/php/plugins/addon/vimeo/Vimeo.php';

        $this->assertTrue(true);
    }

    private function _testOptionsPageUi()
    {
        $gallerySources = array(

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_ALBUM =>
            tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL =>
            tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_SEARCH =>
            tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY =>
            tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN =>
            tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_CREDITED =>
            tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_LIKES =>
            tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE,

            tubepress_plugins_vimeo_api_const_options_values_GallerySourceValue::VIMEO_GROUP =>
            tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE
        );

        foreach ($gallerySources as $name => $value) {

            $mockField = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
            $mockField->shouldReceive('getDesiredTabName')->andReturn('gallery-source');

            $this->_mockFieldBuilder->shouldReceive('build')->once()->with($value,
                tubepress_impl_options_ui_fields_TextField::FIELD_CLASS_NAME, 'gallery-source')->andReturn($mockField);

            $this->_mockVideoProviderRegistry->shouldReceive('registerService')->once()->with(

                tubepress_spi_options_ui_Field::CLASS_NAME,
                Mockery::on(function ($arg) use ($name, $value) {

                    return $arg instanceof tubepress_impl_options_ui_fields_GallerySourceField
                        && $arg->getDesiredTabName() === 'gallery-source'
                        && $arg->getGallerySourceName() === $name;
                }));
        }

        $mockPlayerColorField = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
        $mockVimeoKeyField = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);
        $mockVimeoSecretField = Mockery::mock(tubepress_spi_options_ui_Field::CLASS_NAME);

        $this->_mockOptionsDescriptorReference->shouldReceive('findOneByName')->once()->with(
            tubepress_plugins_vimeo_api_const_options_names_Embedded::PLAYER_COLOR
        )->andReturn($mockPlayerColorField);

        $this->_mockOptionsDescriptorReference->shouldReceive('findOneByName')->once()->with(
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY
        )->andReturn($mockVimeoKeyField);

        $this->_mockOptionsDescriptorReference->shouldReceive('findOneByName')->once()->with(
            tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET
        )->andReturn($mockVimeoSecretField);

        $this->_mockVideoProviderRegistry->shouldReceive('registerService')->once()->with(

            tubepress_spi_options_ui_Field::CLASS_NAME,
            Mockery::type('tubepress_impl_options_ui_fields_ColorField')
        );

        $this->_mockVideoProviderRegistry->shouldReceive('registerService')->twice()->with(

            tubepress_spi_options_ui_Field::CLASS_NAME,
            Mockery::type('tubepress_impl_options_ui_fields_TextField')
        );

    }

    private function _testOptions()
    {
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_KEY);
        $option->setLabel('Vimeo API "Consumer Key"');                                                                                        //>(translatable)<
        $option->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.'); //>(translatable)<
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_Feed::VIMEO_SECRET);
        $option->setLabel('Vimeo API "Consumer Secret"');                                                                                     //>(translatable)<
        $option->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.'); //>(translatable)<
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE);
        $option->setDefaultValue('mattkaar');
        $option->setLabel('Videos uploaded by this Vimeo user');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE);
        $option->setDefaultValue('coiffier');
        $option->setLabel('Videos this Vimeo user likes');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE);
        $option->setDefaultValue('royksopp');
        $option->setLabel('Videos this Vimeo user appears in');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE);
        $option->setDefaultValue('cats playing piano');
        $option->setLabel('Vimeo search for');  //>(translatable)<
        $option->setValidValueRegex('/[\w" ]+/');
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE);
        $option->setDefaultValue('patricklawler');
        $option->setLabel('Videos credited to this Vimeo user (either appears in or uploaded by)');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE);
        $option->setDefaultValue('splitscreenstuff');
        $option->setLabel('Videos in this Vimeo channel');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE);
        $option->setDefaultValue('hdxs');
        $option->setLabel('Videos from this Vimeo group');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);
        $option->setDefaultValue('140484');
        $option->setLabel('Videos from this Vimeo album');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_Meta::LIKES);
        $option->setLabel('Number of "likes"');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_vimeo_api_const_options_names_Embedded::PLAYER_COLOR);
        $option->setDefaultValue('999999');
        $option->setLabel('Main color');              //>(translatable)<
        $option->setDescription('Default is 999999.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
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