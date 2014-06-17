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
 * @covers tubepress_core_html_search_impl_listeners_options_AcceptableValues
 */
class tubepress_test_core_html_search_impl_listeners_options_AcceptableValuesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_search_impl_listeners_options_AcceptableValues
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaProvider;

    public function onSetup()
    {
        $this->_mockMediaProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $this->_sut = new tubepress_core_html_search_impl_listeners_options_AcceptableValues();

        $this->_sut->setMediaProviders(array($this->_mockMediaProvider));
    }

    public function testOnAcceptableValues()
    {
        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(null);
        $mockEvent->shouldReceive('setSubject')->once()->with(array('aaa' => 'display name'));

        $this->_mockMediaProvider->shouldReceive('getName')->once()->andReturn('aaa');
        $this->_mockMediaProvider->shouldReceive('getDisplayName')->once()->andReturn('display name');

        $this->_sut->onAcceptableValues($mockEvent);

        $this->assertTrue(true);
    }
}

