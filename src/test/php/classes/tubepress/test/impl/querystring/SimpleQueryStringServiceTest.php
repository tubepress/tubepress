<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_impl_querystring_SimpleQueryStringServiceTest extends tubepress_test_TubePressUnitTest
{

    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_impl_querystring_SimpleQueryStringService();
    }

    public function testGetFullUrlHttpsOn()
    {
        $serverVars = array("HTTPS" => "on",
                            "SERVER_PORT" => "443",
                            "SERVER_NAME" => "fake.com",
                            "REQUEST_URI" => "/index.html");
        $this->assertEquals("https://fake.com:443/index.html",
            $this->_sut->getFullUrl($serverVars));
    }

    public function testGetFullUrlHttpsOff()
    {
        $serverVars = array("HTTPS" => "off",
                            "SERVER_PORT" => "80",
                            "SERVER_NAME" => "fake.com",
                            "REQUEST_URI" => "/index.html");
        $this->assertEquals("http://fake.com/index.html",
            $this->_sut->getFullUrl($serverVars));
    }
}
