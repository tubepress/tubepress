<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_api_options_listeners_PatternValidatingListener<extended>
 */
class tubepress_test_api_options_listeners_PatternValidatingListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_api_options_listeners_PatternValidatingListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTranslation;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockReference;

    public function onSetup()
    {
        $this->_mockEvent       = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockTranslation = $this->mock(tubepress_api_translation_TranslatorInterface::_);
        $this->_mockReference   = $this->mock(tubepress_api_options_ReferenceInterface::_);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturnNull();
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionName')->andReturn('option-name');
    }

    /**
     * @dataProvider getData
     */
    public function testOnOptionSet($pattern, $incoming, $expectedToPass)
    {
        $this->_sut = new tubepress_api_options_listeners_PatternValidatingListener(

            $pattern, 'foo %s', $this->_mockReference, $this->_mockTranslation
        );

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);

        if (!$expectedToPass) {

            $this->_mockTranslation->shouldReceive('trans')->once()->with('foo %s')->andReturn('omg %s');
            $this->_mockTranslation->shouldReceive('trans')->once()->with('something awesome')->andReturn('holy smokes');
            $this->_mockReference->shouldReceive('getUntranslatedLabel')->twice()->with('option-name')->andReturn('something awesome');
            $this->_mockEvent->shouldReceive('setSubject')->once()->with(array('omg holy smokes'));
            $this->_mockEvent->shouldReceive('stopPropagation')->once();
        }

        $this->_sut->onOptionValidation($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function getData()
    {
        return array(

            array('~[a-z]+~', 'aa', true),
            array('~[a-z]+~', '33', false),
        );
    }
}