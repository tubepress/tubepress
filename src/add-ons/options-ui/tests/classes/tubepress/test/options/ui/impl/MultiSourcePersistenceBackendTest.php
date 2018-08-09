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
 * @covers tubepress_options_ui_impl_MultiSourcePersistenceBackend<extended>
 */
class tubepress_test_app_options_MultiSourcePersistenceBackendTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_options_ui_impl_MultiSourcePersistenceBackend
     */
    private $_sut;

    protected function onSetup()
    {
        $this->_sut = new tubepress_options_ui_impl_MultiSourcePersistenceBackend(array('foo' => 'bar'));
    }

    public function testPersistenceQueue()
    {
        $this->assertEquals(array(), $this->_sut->getPersistenceQueue());

        $this->_sut->saveAll(array('hi' => 'there'));

        $this->assertEquals(array('hi' => 'there'), $this->_sut->getPersistenceQueue());
    }

    public function testCreateEach()
    {
        $expected = array('foo' => 'bar');

        $this->_sut->createEach(array('x' => 'z'));

        $this->assertEquals($expected, $this->_sut->fetchAllCurrentlyKnownOptionNamesToValues());
    }

    public function testGetAllKnown()
    {
        $expected = array('foo' => 'bar');

        $this->assertEquals($expected, $this->_sut->fetchAllCurrentlyKnownOptionNamesToValues());
    }
}
