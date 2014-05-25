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
 * @covers tubepress_wordpress_impl_listeners_html_CssJsDequerer
 */
class tubepress_test_wordpress_impl_listeners_html_CssJsDequererTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_html_CssJsDequerer
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_wordpress_impl_listeners_html_CssJsDequerer();
    }

    public function testCss()
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('setSubject')->once()->with(array());

        $this->_sut->onCss($mockEvent);

        $this->assertTrue(true);
    }

    public function testJs()
    {
        $mockEvent = $this->mock('tubepress_core_api_event_EventInterface');
        $mockEvent->shouldReceive('setSubject')->once()->with(array());

        $this->_sut->onJs($mockEvent);

        $this->assertTrue(true);
    }
}