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
 * @covers tubepress_app_embedded_impl_listeners_options_AcceptableValues
 */
class tubepress_test_app_embedded_impl_listeners_options_AcceptableValuesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_embedded_impl_listeners_options_AcceptableValues
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockEmbeddedPlayers;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockVideoProviders;


    public function onSetup()
    {
        $mockEmbeddedPlayer = $this->mock(tubepress_app_embedded_api_EmbeddedProviderInterface::_);
        $this->_mockEmbeddedPlayers = array($mockEmbeddedPlayer);

        $mockVideoProvider = $this->mock(tubepress_app_media_provider_api_MediaProviderInterface::_);
        $this->_mockVideoProviders = array($mockVideoProvider);

        $this->_sut = new tubepress_app_embedded_impl_listeners_options_AcceptableValues();
        $this->_sut->setEmbeddedProviders($this->_mockEmbeddedPlayers);
        $this->_sut->setMediaProviders($this->_mockVideoProviders);
    }

    public function testAcceptableValues()
    {
        $mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturnNull();

        $mockEmbeddedPlayer = $this->_mockEmbeddedPlayers[0];
        $mockEmbeddedPlayer->shouldReceive('getName')->twice()->andReturn('embedded name');
        $mockEmbeddedPlayer->shouldReceive('getUntranslatedDisplayName')->once()->andReturn('player friendly name');

        $mockVideoProvider = $this->_mockVideoProviders[0];
        $mockVideoProvider->shouldReceive('getName')->once()->andReturn('provider name');
        $mockVideoProvider->shouldReceive('getDisplayName')->once()->andReturn('provider friendly name');

        $mockEvent->shouldReceive('setSubject')->once()->with(array(

            'provider_based' => 'Provider default',
            'embedded name' => 'player friendly name',
        ));

        $this->_sut->onAcceptableValues($mockEvent);

        $this->assertTrue(true);
    }
}