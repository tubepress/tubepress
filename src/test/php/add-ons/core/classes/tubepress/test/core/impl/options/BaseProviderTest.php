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

class tubepress_test_core_impl_options_BaseProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_api_options_ProviderInterface
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockLangUtils;

    public final function onSetup()
    {
        $this->_mockOptionProvider  = $this->mock(tubepress_core_api_options_EasyProviderInterface::_);
        $this->_mockMessageService  = $this->mock(tubepress_core_api_translation_TranslatorInterface::_);
        $this->_mockEventDispatcher = $this->mock('tubepress_core_api_event_EventDispatcherInterface');
        $this->_mockLangUtils       = $this->mock(tubepress_api_util_LangUtilsInterface::_);
        $this->_sut = new tubepress_core_impl_options_BaseProvider(

            $this->_mockOptionProvider,
            $this->_mockMessageService,
            $this->_mockEventDispatcher,
            $this->_mockLangUtils
        );
    }

    public function testInvalidValueByBoolean()
    {
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToDefaultValues')->once()->andReturn(array(

            '1' => true,
            'b' => 'bar',
            'sdf' => 'c'
        ));

        $this->_mockOptionProvider->shouldReceive('getOptionNamesOfPositiveIntegers')->once()->andReturn(array());
        $this->_mockOptionProvider->shouldReceive('getOptionNamesOfNonNegativeIntegers')->once()->andReturn(array());
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToUntranslatedLabels')->once()->andReturn(array(
            '1' => '1 label',
            'b' => 'b label',
            'sdf' => 'c label'
        ));

        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToValidValueRegexes')->once()->andReturn(array(
            'b' => '/sdf.*/'
        ));

        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('XYZ');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('1 label')->andReturn($mockEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_core_api_const_event_EventNames::OPTION_GET_LABEL . ".1", $mockEvent
        );

        $this->_mockMessageService->shouldReceive('_')->once()->with('XYZ')->andReturn('ABC');

        $this->assertEquals('"ABC" can only be "true" or "false". You supplied "foo".', $this->_sut->getProblemMessage('1', 'foo'));
    }

    public function testInvalidValueByRegex()
    {
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToDefaultValues')->once()->andReturn(array(

            '1' => true,
            'b' => 'bar',
            'sdf' => 'c'
        ));

        $this->_mockOptionProvider->shouldReceive('getOptionNamesOfPositiveIntegers')->once()->andReturn(array());
        $this->_mockOptionProvider->shouldReceive('getOptionNamesOfNonNegativeIntegers')->once()->andReturn(array());
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToUntranslatedLabels')->once()->andReturn(array(
            '1' => '1 label',
            'b' => 'b label',
            'sdf' => 'c label'
        ));

        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToValidValueRegexes')->once()->andReturn(array(
            'b' => '/sdf.*/'
        ));

        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('XYZ');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('b label')->andReturn($mockEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_core_api_const_event_EventNames::OPTION_GET_LABEL . ".b", $mockEvent
        );

        $this->_mockMessageService->shouldReceive('_')->once()->with('XYZ')->andReturn('ABC');

        $this->assertEquals('Invalid value supplied for "ABC".', $this->_sut->getProblemMessage('b', 'foo'));
    }

    public function testGetAllOptionNames()
    {
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToDefaultValues')->once()->andReturn(array(

            '1' => 'foo',
            'b' => 'bar',
            'sdf' => 'c'
        ));

        $this->assertEquals(array('1', 'b', 'sdf'), $this->_sut->getAllOptionNames());
    }

    public function testHasOption()
    {
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToDefaultValues')->once()->andReturn(array(

            'x' => '4'
        ));

        $this->assertTrue($this->_sut->hasOption('x'));
        $this->assertFalse($this->_sut->hasOption('a'));
    }

    public function testDefaultValue()
    {
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToDefaultValues')->once()->andReturn(array(

            'b' => 'bar',
        ));

        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('XYZ');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('bar')->andReturn($mockEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_core_api_const_event_EventNames::OPTION_GET_DEFAULT_VALUE . ".b", $mockEvent
        );

        $actual = $this->_sut->getDefaultValue('b');

        $this->assertEquals('XYZ', $actual);
    }

    public function testGetDescription()
    {
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToUntranslatedDescriptions')->once()->andReturn(array(

            'b' => 'bar',
        ));

        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('XYZ');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('bar')->andReturn($mockEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_core_api_const_event_EventNames::OPTION_GET_DESCRIPTION . ".b", $mockEvent
        );

        $this->assertEquals('XYZ', $this->_sut->getDescription('b'));
    }

    public function testGetLabel()
    {
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToUntranslatedLabels')->once()->andReturn(array(

            'b' => 'bar',
        ));

        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('XYZ');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('bar')->andReturn($mockEvent);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_core_api_const_event_EventNames::OPTION_GET_LABEL . ".b", $mockEvent
        );

        $this->assertEquals('XYZ', $this->_sut->getLabel('b'));
    }

    public function testShortcodeSettability()
    {
        $this->_mockOptionProvider->shouldReceive('getOptionNamesThatCannotBeSetViaShortcode')->once()->andReturn(array(

            'b'
        ));
        $this->assertTrue($this->_sut->isAbleToBeSetViaShortcode('a'));
        $this->assertFalse($this->_sut->isAbleToBeSetViaShortcode('b'));
    }

    public function testIsPro()
    {
        $this->_mockOptionProvider->shouldReceive('getAllProOptionNames')->once()->andReturn(array(

            'a'
        ));
        $this->assertTrue($this->_sut->isProOnly('a'));
        $this->assertFalse($this->_sut->isProOnly('b'));
    }

    public function testBoolean()
    {
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToDefaultValues')->once()->andReturn(array(

            'a' => true,
            'b' => 'c'
        ));
        $this->assertTrue($this->_sut->isBoolean('a'));
        $this->assertFalse($this->_sut->isBoolean('b'));
    }

    public function testPersistability()
    {
        $this->_mockOptionProvider->shouldReceive('getOptionsNamesThatShouldNotBePersisted')->once()->andReturn(array(

            'b'
        ));
        $this->assertTrue($this->_sut->isMeantToBePersisted('a'));
        $this->assertFalse($this->_sut->isMeantToBePersisted('b'));


    }

    public function testValidateNoSuchOption()
    {
        $this->_mockOptionProvider->shouldReceive('getMapOfOptionNamesToDefaultValues')->once()->andReturn(array());
        $this->assertFalse($this->_sut->isValid('no such option', 'some candidate'));
        $this->assertEquals('No option with name "no such option".', $this->_sut->getProblemMessage('no such option', 'some candidate'));
    }


    protected function doTestGetDiscreteAcceptableValues($optionName, $expected)
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn('XYZ');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($expected)->andReturn($mockEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(

            tubepress_core_api_const_event_EventNames::OPTION_GET_DISCRETE_ACCEPTABLE_VALUES . ".$optionName", $mockEvent
        );

        $this->assertEquals('XYZ', $this->_sut->getDiscreteAcceptableValues($optionName));
    }

}