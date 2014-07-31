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
 * @covers tubepress_app_api_listeners_options_RegexValidatingListener<extended>
 */
class tubepress_test_app_api_listeners_options_RegexValidatingListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_api_listeners_options_RegexValidatingListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslation;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockReference;

    public function onSetup()
    {
        $this->_mockEvent       = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockTranslation = $this->mock(tubepress_lib_api_translation_TranslatorInterface::_);
        $this->_mockReference   = $this->mock(tubepress_app_api_options_ReferenceInterface::_);

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturnNull();
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionName')->andReturn('option-name');
    }

    /**
     * @dataProvider getData
     */
    public function testOnOptionSet($type, $incoming, $expectedToPass)
    {
        $this->_sut = new tubepress_app_api_listeners_options_RegexValidatingListener($type, $this->_mockReference, $this->_mockTranslation);

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);

        if (!$expectedToPass) {

            $this->_mockTranslation->shouldReceive('_')->once()->with('Invalid value supplied for "%s".')->andReturn('omg %s');
            $this->_mockTranslation->shouldReceive('_')->once()->with('something awesome')->andReturn('holy smokes');
            $this->_mockReference->shouldReceive('getUntranslatedLabel')->twice()->with('option-name')->andReturn('something awesome');
            $this->_mockEvent->shouldReceive('setSubject')->once()->with(array('omg holy smokes'));
        }

        $this->_sut->onOption($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function getData()
    {
        return array(

            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE, 1, true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE, '1', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE, 0, false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE, '0', false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE, -1, false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_POSITIVE, '-1', false),

            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE, 1, true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE, '1', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE, 0, true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE, '0', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE, -1, false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_INTEGER_NONNEGATIVE, '-1', false),

            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS, '', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS, 'x', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS, '3', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS, 3, true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS, '-', false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ZERO_OR_MORE_WORDCHARS, '_', true),

            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS, '', false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS, 'x', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS, '3', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS, 3, true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS, '-', false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS, '_', true),

            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN, '', false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN, 'x', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN, '3', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN, 3, true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN, '-', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_ONE_OR_MORE_WORDCHARS_OR_HYPHEN, '_', true),

            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_HEXCOLOR, '', false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_HEXCOLOR, 'x', false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_HEXCOLOR, 'aaaaaa', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_HEXCOLOR, 111222, true),

            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_YOUTUBE_VIDEO_ID, '12345678901', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_YOUTUBE_VIDEO_ID, '1234567890', false),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_YOUTUBE_VIDEO_ID, '1234567890-', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_YOUTUBE_VIDEO_ID, '1234567890_', true),
            array(tubepress_app_api_listeners_options_RegexValidatingListener::TYPE_STRING_YOUTUBE_VIDEO_ID, '1234567890&', false),
        );
    }
}