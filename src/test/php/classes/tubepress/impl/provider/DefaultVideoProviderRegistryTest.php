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
class tubepress_impl_provider_DefaultVideoProviderRegistryTest extends TubePressUnitTest
{
    private $_sut;

    function setUp()
    {
        $this->_sut = new tubepress_impl_provider_DefaultVideoProviderRegistry();
    }

    public function testNoProvidersRegistered()
    {
        $this->assertEquals(array(), $this->_sut->getAllRegisteredProviders());
    }

    public function testRegisterProvider()
    {
        $mock = Mockery::mock(tubepress_spi_provider_VideoProvider::_);
        $mock->shouldReceive('getName')->twice()->andReturn('x');

        $result = $this->_sut->registerProvider($mock);

        $registered = $this->_sut->getAllRegisteredProviders();

        $this->assertNull($result);
        $this->assertSame($registered[0], $mock);
    }

    public function testRegisterProviderTwice()
    {
        $mock = Mockery::mock(tubepress_spi_provider_VideoProvider::_);
        $mock->shouldReceive('getName')->times(2)->andReturn('x');

        $result = $this->_sut->registerProvider($mock);
        $this->assertNull($result);

        $mock2 = Mockery::mock(tubepress_spi_provider_VideoProvider::_);
        $mock2->shouldReceive('getName')->times(2)->andReturn('x');

        $result = $this->_sut->registerProvider($mock2);

        $this->assertEquals('x has already been registered', $result);

        $this->assertTrue(count($this->_sut->getAllRegisteredProviders()) === 1);
    }
}