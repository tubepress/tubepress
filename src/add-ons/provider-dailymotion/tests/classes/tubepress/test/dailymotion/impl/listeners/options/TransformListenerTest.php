<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_dailymotion_impl_listeners_options_TransformListener
 */
class tubepress_test_dailymotion_impl_listeners_options_TransformListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var \Mockery\MockInterface;
     */
    private $_mockTransformer;

    /**
     * @var \Mockery\MockInterface;
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockTransformer = $this->mock('stdClass');
        $this->_mockEvent       = $this->mock('tubepress_api_event_EventInterface');
    }

    /**
     * @dataProvider getData
     */
    public function testTransform($incoming, $outgoing, $allowEmpty)
    {
        $this->_mockEvent->shouldReceive('getArgument')->once()->with('optionValue')->andReturn($incoming);

        if (!$outgoing && !$allowEmpty) {

            $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array('foobar'));
            $this->_mockEvent->shouldReceive('setSubject')->once()->with(array('foobar', 'error message'));

        } else {

            $this->_mockEvent->shouldReceive('setArgument')->once()->with('optionValue', $outgoing)->andReturn($incoming);
        }

        $this->_mockTransformer->shouldReceive('transform')->once()->with($incoming)->andReturn($outgoing);

        $sut = new tubepress_dailymotion_impl_listeners_options_TransformListener(
            $this->_mockTransformer,
            'error message',
            $allowEmpty
        );

        $sut->onOption($this->_mockEvent);
    }

    public function getData()
    {
        return array(
            array('hi', 'there', false),
            array('hi', '', false),
        );
    }
}
