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
class tubepress_plugins_wordpress_impl_listeners_WordPressOptionsRegistrarTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockOptionsDescriptorReference;

    public function setUp()
    {
        $this->_sut = new tubepress_plugins_wordpress_impl_listeners_WordPressOptionsRegistrar();

        $this->_mockOptionsDescriptorReference = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionsDescriptorReference);
    }

    public function testOptions()
    {
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_wordpress_api_const_options_names_WordPress::WIDGET_TITLE);
        $option->setDefaultValue('TubePress');
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_plugins_wordpress_api_const_options_names_WordPress::WIDGET_SHORTCODE);
        $option->setDefaultValue('[tubepress thumbHeight=\'105\' thumbWidth=\'135\']');
        $this->_verifyOption($option);



        $this->_sut->onBoot(new tubepress_api_event_TubePressEvent());

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
                   && $registeredOption->isApplicableToAllProviders() === $expectedOption->isApplicableToAllProviders()
                   && $registeredOption->isApplicableToVimeo() === $expectedOption->isApplicableToVimeo()
                   && $registeredOption->isApplicableToYouTube() === $expectedOption->isApplicableToYouTube()
                   && $registeredOption->isBoolean() === $expectedOption->isBoolean()
                   && $registeredOption->isMeantToBePersisted() === $expectedOption->isMeantToBePersisted()
                   && $registeredOption->hasDiscreteAcceptableValues() === $expectedOption->hasDiscreteAcceptableValues()
                   && $registeredOption->isProOnly() === $expectedOption->isProOnly();
        }));
    }

}
