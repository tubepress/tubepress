<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_impl_querystring_SimpleQueryStringServiceTest extends TubePressUnitTest
{

    private $_sut;

    public function setup()
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
