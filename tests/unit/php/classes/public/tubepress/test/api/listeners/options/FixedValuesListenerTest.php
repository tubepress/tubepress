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
 * @covers tubepress_api_options_listeners_FixedValuesListener<extended>
 */
class tubepress_test_api_listeners_options_FixedValuesListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_api_options_listeners_FixedValuesListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockEvent = $this->mock('tubepress_api_event_EventInterface');

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturnNull();

        $this->_sut = new tubepress_api_options_listeners_FixedValuesListener(

            array('foo' => 'bar')
        );
    }

    public function testOnOption()
    {
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array(

            'foo' => 'bar'
        ));

        $this->_sut->onAcceptableValues($this->_mockEvent);

        $this->assertTrue(true);
    }
}