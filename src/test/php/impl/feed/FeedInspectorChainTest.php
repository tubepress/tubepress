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
class tubepress_impl_feed_FeedInspectorChainTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_mockChain;

    private $_mockProviderCalculator;

    function setUp()
    {
        $this->_mockChain              = Mockery::mock('ehough_chaingang_api_Chain');
        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator($this->_mockProviderCalculator);

        $this->_sut                    = new tubepress_impl_feed_FeedInspectorChain($this->_mockChain);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    function testCountCouldNotHandle()
    {
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('videoProvider');


        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::on(function ($context) {

                $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_COUNT, 1);

                return $context->get(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_PROVIDER_NAME) === 'videoProvider'
                    && $context->get(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_RAW_FEED) === 'rawfeed';
            }
        ))->andReturn(false);
        $result = $this->_sut->getTotalResultCount('rawfeed');

        $this->assertTrue($result === 0);
    }

    function testCount()
    {
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('videoProvider');

        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::on(function ($context) {

                $context->put(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_COUNT, 'foobar');

                return $context->get(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_PROVIDER_NAME) === 'videoProvider'
                    && $context->get(tubepress_impl_feed_FeedInspectorChain::CHAIN_KEY_RAW_FEED) === 'rawfeed';
            }
        ))->andReturn(true);


        $this->assertEquals('foobar', $this->_sut->getTotalResultCount('rawfeed'));
    }
}
