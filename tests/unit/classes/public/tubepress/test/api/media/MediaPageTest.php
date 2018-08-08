<?php
/**
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_api_media_MediaPage
 */
class tubepress_test_api_media_MediaPage extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_api_media_MediaPage
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_api_media_MediaPage();
    }

    public function testSetNonIntegralTotal()
    {
        $this->_sut->setTotalResultCount('50.1');
        $this->assertEquals(50, $this->_sut->getTotalResultCount());
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetNonNumericTotal()
    {
        $this->_sut->setTotalResultCount('something bad');
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetNegativeTotal()
    {
        $this->_sut->setTotalResultCount(-1501);
    }

    public function testSetGetVideos()
    {
        $vids = array('hello');
        $this->_sut->setItems($vids);
        $this->assertEquals($vids, $this->_sut->getItems());
    }

    public function testSetGetTotal()
    {
        $this->_sut->setTotalResultCount(501);
        $this->assertEquals(501, $this->_sut->getTotalResultCount());
    }
}

