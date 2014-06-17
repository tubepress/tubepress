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

/**
 * @covers tubepress_core_options_impl_easy_EasyValidator<extended>
 */
class tubepress_test_core_options_impl_easy_EasyValidatorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    public function onSetup()
    {
        $this->_mockTranslator = $this->mock(tubepress_core_translation_api_TranslatorInterface::_);
        $this->_mockReference  = $this->mock(tubepress_core_options_api_ReferenceInterface::_);
    }

    /**
     * @dataProvider getData
     */
    public function testPositiveInteger($type, $candidate, $pass)
    {
        $sut = new tubepress_core_options_impl_easy_EasyValidator($type, $this->_mockReference, $this->_mockTranslator);

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array());
        $mockEvent->shouldReceive('getArgument')->once()->with('optionName')->andReturn('name');
        $mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($candidate);

        if (!$pass) {

            $this->_mockReference->shouldReceive('getUntranslatedLabel')->twice()->with('name')->andReturn('NAME');
            $this->_mockTranslator->shouldReceive('_')->once()->with('NAME')->andReturn('<<name>>');
            $this->_mockTranslator->shouldReceive('_')->once()->with('Invalid value supplied for "%s".')->andReturn('abc %s');
            $mockEvent->shouldReceive('setSubject')->once()->with(array('abc <<name>>'));
        }

        $sut->onOption($mockEvent);
        $this->assertTrue(true);
    }

    public function getData()
    {
        return array(

            array('positiveInteger', 55, true),
            array('positiveInteger', '55', true),
            array('positiveInteger', '55.0', false),
            array('positiveInteger', -55, false),
            array('positiveInteger', '-55', false),
            array('positiveInteger', '-55.0', false),
            array('positiveInteger', new stdClass(), true),
            array('positiveInteger', array(), true),
            array('nonNegativeInteger', 0, true),
            array('nonNegativeInteger', 55, true),
        );
    }
}