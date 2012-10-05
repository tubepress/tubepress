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
class tubepress_impl_patterns_sl_ServiceCollectionsRegistryTest extends TubePressUnitTest
{
    private $_sut;
    
    public function __construct()
    {
        $this->_sut = new tubepress_impl_patterns_sl_DefaultServiceCollectionsRegistry();
    }
    
    public function testRegisterService()
    {
        $foo = new stdClass();

        $this->_sut->registerService('x', $foo);

        $result = $this->_sut->getAllServicesOfType('x');

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 1);

        $this->assertSame($foo, $result[0]);
    }

    public function testRegisterTwoServices()
    {
        $foo = new stdClass();
        $bar = new stdClass();

        $this->_sut->registerService('x', $foo);
        $this->_sut->registerService('x', $bar);

        $result = $this->_sut->getAllServicesOfType('x');

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 2);

        $this->assertSame($foo, $result[0]);
        $this->assertSame($bar, $result[1]);
    }

    public function testGetAllServicesNoneExist()
    {
        $result = $this->_sut->getAllServicesOfType('x');

        $this->assertTrue(is_array($result));
        $this->assertTrue(count($result) === 0);
    }

}