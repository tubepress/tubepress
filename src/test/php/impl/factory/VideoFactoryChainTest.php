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
class org_tubepress_impl_factory_VideoFactoryChainTest extends PHPUnit_Framework_TestCase
{
    /** @var tubepress_impl_factory_VideoFactoryChain */
    private $_sut;

    private $_mockChain;

    private $_mockProviderCalculator;

    private $_mockEventDispatcher;

    public function setUp()
    {
        $this->_mockChain              = Mockery::mock('ehough_chaingang_api_Chain');
        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);
        $this->_mockEventDispatcher    = Mockery::mock('ehough_tickertape_api_IEventDispatcher');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator($this->_mockProviderCalculator);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);

        $this->_sut = new tubepress_impl_factory_VideoFactoryChain($this->_mockChain);
    }

    public function testNobodyCanHandle()
    {
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('providerrr');

        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::type('ehough_chaingang_api_Context'))->andReturn(false);

        $this->assertEquals(array(), $this->_sut->feedToVideoArray('bla'));
    }

    public function testConvert()
    {
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('providerrr');

        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) {

            $arg->put(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_VIDEO_ARRAY, array('a', 'b', 'c'));

            return $arg instanceof ehough_chaingang_api_Context && $arg->get(tubepress_impl_factory_VideoFactoryChain::CHAIN_KEY_RAW_FEED) === 'bla';

        }))->andReturn(true);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(
            tubepress_api_event_VideoConstruction::EVENT_NAME, Mockery::on(function ($arg) {

            return $arg instanceof tubepress_api_event_VideoConstruction && ($arg->getSubject() === 'a' ||
                $arg->getSubject() === 'b' || $arg->getSubject() === 'c') && $arg->getArgument(tubepress_api_event_VideoConstruction::ARGUMENT_PROVIDER_NAME) === 'providerrr';
        }));

        $this->assertEquals(array('a', 'b', 'c'), $this->_sut->feedToVideoArray('bla'));
    }
}