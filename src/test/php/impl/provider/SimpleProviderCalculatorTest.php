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
class tubepress_impl_player_SimpleProviderCalculatorTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_mockExecutionContext;

    function setUp()
    {
        $this->_mockExecutionContext   = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

        $this->_sut = new tubepress_impl_provider_SimpleProviderCalculator();
    }

    public function testCalcVideoVimeo()
    {
        $this->assertTrue($this->_sut->calculateProviderOfVideoId('3994857') === tubepress_spi_provider_Provider::VIMEO);
        $this->assertFalse($this->_sut->calculateProviderOfVideoId('3994857X') === tubepress_spi_provider_Provider::VIMEO);
    }
}