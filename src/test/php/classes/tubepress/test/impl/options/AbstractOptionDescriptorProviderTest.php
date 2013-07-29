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

abstract class tubepress_test_impl_options_AbstractOptionDescriptorProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_spi_options_PluggableOptionDescriptorProvider
     */
    private $_sut;

    public final function onSetup()
    {
        $this->_sut = $this->buildSut();
    }

    public function testCorrectOptions()
    {
        $this->prepare($this->_sut);

        $expected = $this->getExpectedOptions();
        $actual   = $this->_sut->getOptionDescriptors();

        $this->assertTrue(is_array($expected));
        $this->assertTrue(is_array($actual));

        $this->_ensureAllOptionDescriptors($expected);
        $this->_ensureAllOptionDescriptors($actual);

        $this->assertTrue(count($expected) === count($actual));

        $expectedMap = $this->_toMap($expected);
        $actualMap   = $this->_toMap($actual);

        ksort($expectedMap);
        ksort($actualMap);

        $this->assertEquals(array_keys($expectedMap), array_keys($actualMap));

        foreach ($expectedMap as $name => $option) {

            $actual = $actualMap[$name];

            $this->_verifyEqual($option, $actual);
        }
    }

    /**
     * @return tubepress_spi_options_PluggableOptionDescriptorProvider
     */
    protected abstract function buildSut();

    /**
     * @return tubepress_spi_options_OptionDescriptor[]
     */
    protected abstract function getExpectedOptions();

    protected function prepare(tubepress_spi_options_PluggableOptionDescriptorProvider $sut)
    {
        //override point
    }

    private function _toMap(array $options)
    {
        $toReturn = array();

        foreach ($options as $option) {

            $toReturn[$option->getName()] = $option;
        }

        return $toReturn;
    }

    private function _ensureAllOptionDescriptors(array $arr)
    {
        foreach ($arr as $candidate) {

            if (!($candidate instanceof tubepress_spi_options_OptionDescriptor)) {

                $this->fail(sprintf('%s returned a non tubepress_spi_options_OptionDescriptor', get_class($this->_sut)));
            }
        }
    }

    private function _verifyEqual(tubepress_spi_options_OptionDescriptor $expectedOption, tubepress_spi_options_OptionDescriptor $actualOption)
    {
        $ok = $actualOption->getAcceptableValues() === $expectedOption->getAcceptableValues()
            && $actualOption->getAliases() === $expectedOption->getAliases()
            && $actualOption->getDefaultValue() === $expectedOption->getDefaultValue()
            && $actualOption->getDescription() === $expectedOption->getDescription()
            && $actualOption->getLabel() === $expectedOption->getLabel()
            && $actualOption->getName() === $expectedOption->getName()
            && $actualOption->getValidValueRegex() === $expectedOption->getValidValueRegex()
            && $actualOption->isAbleToBeSetViaShortcode() === $expectedOption->isAbleToBeSetViaShortcode()
            && $actualOption->isBoolean() === $expectedOption->isBoolean()
            && $actualOption->isMeantToBePersisted() === $expectedOption->isMeantToBePersisted()
            && $actualOption->hasDiscreteAcceptableValues() === $expectedOption->hasDiscreteAcceptableValues()
            && $actualOption->isProOnly() === $expectedOption->isProOnly();

        if (!$ok) {

            $this->fail($expectedOption->getName() . ' did not meet expectations');
        }
    }
}