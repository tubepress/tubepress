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
 * @covers tubepress_internal_http_oauth_v2_Token
 */
class tubepress_test_internal_http_oauth_v2_TokenTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_internal_http_oauth_v2_Token
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_internal_http_oauth_v2_Token();
    }

    public function testIsExpired()
    {
        $inTheFuture = time() + 200;
        $this->_sut->setEndOfLifeUnixTime($inTheFuture);
        $this->assertFalse($this->_sut->isExpired());

        $inThePast = time() - 200;
        $this->_sut->setEndOfLifeUnixTime($inThePast);
        $this->assertTrue($this->_sut->isExpired());

        $now = time();
        $this->_sut->setEndOfLifeUnixTime($now);
        $this->assertFalse($this->_sut->isExpired());

        $then = time() + 200;
        $this->_sut->setLifetimeInSeconds(200);
        $this->assertFalse($this->_sut->isExpired());
        $this->assertEquals($then, $this->_sut->getEndOfLifeUnixTime());
    }

    public function testMarkAsNeverExpires()
    {
        $this->_sut->markAsNeverExpires();
        $this->assertEquals(tubepress_api_http_oauth_v2_TokenInterface::EOL_NEVER_EXPIRES, $this->_sut->getEndOfLifeUnixTime());
        $this->assertFalse($this->_sut->isExpired());
    }

    public function testSetEol()
    {
        $this->_sut->setEndOfLifeUnixTime(999);
        $this->assertEquals(999, $this->_sut->getEndOfLifeUnixTime());
    }

    public function testSetExtraParams()
    {
        $this->_sut->setExtraParams(array('foo', 'bar'));
        $this->assertEquals(array('foo', 'bar'), $this->_sut->getExtraParams());
    }

    public function testSetRefreshToken()
    {
        $this->_sut->setRefreshToken('abc');
        $this->assertEquals('abc', $this->_sut->getRefreshToken());
    }

    public function testSetAccessToken()
    {
        $this->_sut->setAccessToken('abc');
        $this->assertEquals('abc', $this->_sut->getAccessToken());
    }

    public function testDefaults()
    {
        $this->assertFalse($this->_sut->isExpired());
        $this->assertNull($this->_sut->getAccessToken());
        $this->assertNull($this->_sut->getRefreshToken());
        $this->assertEquals(tubepress_api_http_oauth_v2_TokenInterface::EOL_UNKNOWN, $this->_sut->getEndOfLifeUnixTime());
        $this->assertEquals(array(), $this->_sut->getExtraParams());
    }

}
