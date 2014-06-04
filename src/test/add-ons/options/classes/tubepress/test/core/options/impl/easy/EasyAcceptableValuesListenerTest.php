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
 * @covers tubepress_core_options_impl_easy_EasyAcceptableValuesListener<extended>
 */
class tubepress_test_core_options_impl_easy_EasyAcceptableValuesListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @dataProvider getDataForTest
     */
    public function testValues(array $values, $subject, array $expected)
    {
        $sut = new tubepress_core_options_impl_easy_EasyAcceptableValuesListener($values);

        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($subject);
        $event->shouldReceive('setSubject')->once()->with($expected);

        $sut->onAcceptableValues($event);

        $this->assertTrue(true);
    }

    public function getDataForTest()
    {
        return array(

            array(array('foo', 'bar'), null, array('foo', 'bar')),
            array(array('foo', 'bar'), array('baz'), array('baz', 'foo', 'bar')),
            array(array('foo' => 'bar'), array('fooz' => 'baz'), array('foo' => 'bar', 'fooz' => 'baz')),
            array(array('foo' => 'bar'), null, array('foo' => 'bar'))
        );
    }
}