<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_listeners_wpfilter_PhotonListener
 */
class tubepress_test_wordpress_impl_listeners_wpfilter_PhotonListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wpfilter_PhotonListener
     */
    private $_sut;

    public function onSetup()
    {

        $this->_sut = new tubepress_wordpress_impl_listeners_wpfilter_PhotonListener(

            new tubepress_url_impl_puzzle_UrlFactory(),
            new tubepress_util_impl_StringUtils(),
            array('cnn.com', 'abc.com')
        );
    }

    /**
     * @param $incomingUrl
     *
     * @dataProvider getData
     */
    public function testPhoton($incomingUrl, $disablePhoton = false)
    {
        $mockIncomingEvent = $this->mock('tubepress_api_event_EventInterface');

        $mockIncomingEvent->shouldReceive('getArgument')->once()->with('args')->andReturn(array($incomingUrl));

        if ($disablePhoton) {

            $mockIncomingEvent->shouldReceive('setSubject')->once()->with(true);
        }

        $this->_sut->onFilter_jetpack_photon_skip_for_url($mockIncomingEvent);
    }

    public function getData()
    {
        return array(
            array(new stdClass()),
            array(''),
            array('http://cnn.com/', true),
            array('https://i.abc.com/foo.jpg', true),
        );
    }
}
