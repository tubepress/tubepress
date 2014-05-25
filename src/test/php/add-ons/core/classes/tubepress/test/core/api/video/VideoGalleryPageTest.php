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
class tubepress_test_api_video_VideoGalleryPageTest extends tubepress_test_TubePressUnitTest
{
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_core_api_video_VideoGalleryPage();
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
        $this->_sut->setVideos($vids);
        $this->assertEquals($vids, $this->_sut->getVideos());
    }

    public function testSetGetTotal()
    {
        $this->_sut->setTotalResultCount(501);
        $this->assertEquals(501, $this->_sut->getTotalResultCount());
    }
}

