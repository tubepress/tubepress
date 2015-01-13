<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_lib_impl_event_tickertape_EventBase
 */
class tubepress_test_lib_impl_event_tickertape_EventBaseTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_lib_impl_event_tickertape_EventBase
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_lib_impl_event_tickertape_EventBase();
    }

    public function testBasics()
    {
        $this->assertInstanceOf('tubepress_lib_api_event_EventInterface', $this->_sut);
        $this->assertNull($this->_sut->getSubject());
        $this->assertEquals(array(), $this->_sut->getArguments());
    }

    public function testSubject()
    {
        $this->_sut->setSubject('foo');

        $this->assertEquals('foo', $this->_sut->getSubject());
    }
}
