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
 * @covers tubepress_api_ioc_Reference
 */
class tubepress_test_api_ioc_ReferenceTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_api_ioc_Reference
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_api_ioc_Reference('hello');
    }

    public function testToString()
    {
        $this->assertEquals('hello', $this->_sut->__toString());
    }
}
