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

abstract class tubepress_test_impl_options_AbstractOptionProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_spi_options_OptionProvider
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionProvider;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public final function onSetup()
    {
        $this->_sut = $this->buildSut();

        $this->_mockOptionProvider  = $this->createMockSingletonService(tubepress_spi_options_OptionProvider::_);
        $this->_mockMessageService  = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockEventDispatcher = $this->createMockSingletonService('tubepress_api_event_EventDispatcherInterface');
    }

    public function testGetAllOptionNames()
    {
        $this->assertEquals(array_keys($this->getMapOfOptionNamesToDefaultValues()), $this->_sut->getAllOptionNames());
    }

    public function testHasOption()
    {
        $optionNames = $this->getMapOfOptionNamesToDefaultValues();
        $ok          = true;

        foreach ($optionNames as $optionName => $defaultValue) {

            $ok = $ok && $this->_sut->hasOption($optionName);

            if (!$ok) {

                $this->fail('Option provider is missing ' . $optionName);
                return;
            }
        }

        $this->assertTrue($ok);
    }

    public function testDefaultValue()
    {
        $map = $this->getMapOfOptionNamesToDefaultValues();
        $ok  = true;

        foreach ($map as $optionName => $value) {

            $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

                tubepress_api_const_event_EventNames::OPTION_GET_DEFAULT_VALUE . ".$optionName",
                ehough_mockery_Mockery::on(function ($event) use ($value) {

                    $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === $value;

                    $event->setSubject('XYZ');

                    return $ok;
                })
            );

            $actual = $this->_sut->getDefaultValue($optionName);
            $ok     = $ok && $actual === 'XYZ';

            if (!$ok) {

                $this->fail(sprintf('Wrong default value for %s. Expected XYZ but got %s.', $optionName, print_r($actual, true)));
                return;
            }
        }

        $this->assertTrue($ok);
    }

    public function testGetDescription()
    {
        $options          = $this->getMapOfOptionNamesToUntranslatedDescriptions();
        $randomOptionName = array_rand($options);

        if ($randomOptionName === null) {

            $this->assertNull($randomOptionName);
            return;
        }

        $desc = $options[$randomOptionName];

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_EventNames::OPTION_GET_DESCRIPTION . ".$randomOptionName",
            ehough_mockery_Mockery::on(function ($event) use ($desc) {

                $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === $desc;

                $event->setSubject('XYZ');

                return $ok;
            })
        );

        $this->assertEquals('XYZ', $this->_sut->getDescription($randomOptionName));
    }

    public function testGetLabel()
    {
        $options          = $this->getMapOfOptionNamesToUntranslatedLabels();
        $randomOptionName = array_rand($options);

        if ($randomOptionName === null) {

            $this->assertNull($randomOptionName);
            return;
        }

        $desc = $options[$randomOptionName];

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_EventNames::OPTION_GET_LABEL . ".$randomOptionName",
            ehough_mockery_Mockery::on(function ($event) use ($desc) {

                $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === $desc;

                $event->setSubject('XYZ');

                return $ok;
            })
        );

        $this->assertEquals('XYZ', $this->_sut->getLabel($randomOptionName));
    }

    public function testShortcodeSettability()
    {
        $allOptions                         = array_keys($this->getMapOfOptionNamesToDefaultValues());
        $optionsThatCannotBeSetViaShortcode = $this->getOptionNamesThatCannotBeSetViaShortcode();

        if (empty($optionsThatCannotBeSetViaShortcode)) {

            foreach ($allOptions as $optionName) {

                $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode($optionName));
            }

        } else {

            foreach (array_diff($allOptions, $optionsThatCannotBeSetViaShortcode) as $optionName) {

                $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode($optionName));
            }

            foreach ($optionsThatCannotBeSetViaShortcode as $optionName) {

                $this->assertFalse($this->_sut->isAbleToBeSetViaShortcode($optionName));
            }
        }
    }

    public function testIsPro()
    {
        $allOptions = array_keys($this->getMapOfOptionNamesToDefaultValues());
        $proOptions = $this->getAllProOptionNames();

        if (empty($proOptions)) {

            foreach ($allOptions as $optionName) {

                $this->assertFalse($this->_sut->isProOnly($optionName));
            }

        } else {

            foreach (array_diff($allOptions, $proOptions) as $nonProOption) {

                $this->assertFalse($this->_sut->isProOnly($nonProOption));
            }

            foreach ($proOptions as $proOption) {

                $this->assertTrue($this->_sut->isProOnly($proOption));
            }
        }
    }

    public function testBoolean()
    {
        $allOptions     = $this->getMapOfOptionNamesToDefaultValues();
        $allOptionNames = array_keys($allOptions);
        $booleanOptions = array();

        foreach ($allOptions as $name => $value) {

            if (is_bool($value)) {

                $booleanOptions[] = $name;
            }
        }

        foreach ($allOptionNames as $optionName) {

            $this->assertEquals(in_array($optionName, $booleanOptions), $this->_sut->isBoolean($optionName));
        }
    }

    public function testPersistability()
    {
        $allOptions                      = array_keys($this->getMapOfOptionNamesToDefaultValues());
        $optionsThatShouldNotBePersisted = $this->getOptionsNamesThatShouldNotBePersisted();

        if (empty($optionsThatShouldNotBePersisted)) {

            foreach ($allOptions as $optionName) {

                $this->assertTrue($this->_sut->isMeantToBePersisted($optionName));
            }

        } else {

            foreach (array_diff($allOptions, $optionsThatShouldNotBePersisted) as $optionName) {

                $this->assertTrue($this->_sut->isMeantToBePersisted($optionName));
            }

            foreach ($optionsThatShouldNotBePersisted as $optionName) {

                $this->assertFalse($this->_sut->isMeantToBePersisted($optionName));
            }
        }
    }

    public function testValidateNoSuchOption()
    {
        $this->assertFalse($this->_sut->isValid('no such option', 'some candidate'));
        $this->assertEquals('No option with name "no such option".', $this->_sut->getProblemMessage('no such option', 'some candidate'));
    }

    protected function ensureInvalidValueByRegex($optionName, $value)
    {
        $labelMap      = $this->getMapOfOptionNamesToUntranslatedLabels();
        $expectedLabel = $labelMap[$optionName];

        $this->_mockMessageService->shouldReceive('_')->once()->with('XYZ')->andReturn('abc');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_EventNames::OPTION_GET_LABEL . ".$optionName",
            ehough_mockery_Mockery::on(function ($event) use ($expectedLabel) {

                $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === $expectedLabel;

                $event->setSubject('XYZ');

                return $ok;
            })
        );

        $this->assertFalse($this->_sut->isValid($optionName, $value));
    }

    protected function doTestGetDiscreteAcceptableValues($optionName, $expected)
    {
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_api_const_event_EventNames::OPTION_GET_DISCRETE_ACCEPTABLE_VALUES . ".$optionName",
            ehough_mockery_Mockery::on(function ($event) use ($expected) {

                $ok = $event instanceof tubepress_api_event_EventInterface && $event->getSubject() === $expected;

                $event->setSubject('XYZ');

                return $ok;
            })
        );

        $this->assertEquals('XYZ', $this->_sut->getDiscreteAcceptableValues($optionName));
    }

    protected abstract function getMapOfOptionNamesToDefaultValues();

    protected abstract function getMapOfOptionNamesToUntranslatedLabels();

    protected abstract function getMapOfOptionNamesToUntranslatedDescriptions();

    protected abstract function buildSut();

    /**
     * @return tubepress_spi_options_OptionProvider
     */
    protected function getSut()
    {
        return $this->_sut;
    }

    protected function getMockMessageService()
    {
        return $this->_mockMessageService;
    }

    protected function getMockEventDispatcher()
    {
        return $this->_mockEventDispatcher;
    }

    protected function getOptionsNamesThatShouldNotBePersisted()
    {
        //override point
        return array();
    }

    /**
     * @return string[] An array, which may be empty but not null, of Pro option names from this provider.
     */
    protected function getAllProOptionNames()
    {
        //override point
        return array();
    }

    protected function getOptionNamesThatCannotBeSetViaShortcode()
    {
        //override point
        return array();
    }
}