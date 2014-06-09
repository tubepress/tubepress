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
 * @covers tubepress_core_media_provider_impl_listeners_options_AcceptableValues
 */
class tubepress_test_core_media_provider_impl_listeners_options_AcceptableValuesTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_media_provider_impl_listeners_options_AcceptableValues
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMediaProvider;

    public function onSetup()
    {
        $this->_mockMediaProvider = $this->mock(tubepress_core_media_provider_api_MediaProviderInterface::_);
        $this->_mockEvent         = $this->mock('tubepress_core_event_api_EventInterface');

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturnNull();

        $this->_sut = new tubepress_core_media_provider_impl_listeners_options_AcceptableValues();

        $this->_sut->setMediaProviders(array($this->_mockMediaProvider));
    }

    /**
     * @dataProvider getData
     */
    public function testEvent($event, $methodName, $result)
    {
        $this->_mockMediaProvider->shouldReceive($methodName)->once()
            ->andReturn($result);

        $this->_mockEvent->shouldReceive('setSubject')->once()->with($result);

        $this->_sut->$event($this->_mockEvent);

        $this->assertTrue(true);
    }

    public function getData()
    {
        return array(

            array('onOrderBy',     'getMapOfFeedSortNamesToUntranslatedLabels', array('sortName' => 'sort label')),
            array('onMode',        'getGallerySourceNames', array('sourceName')),
            array('onPerPageSort', 'getMapOfPerPageSortNamesToUntranslatedLabels', array('perPage' => 'per page label')),
        );
    }
}