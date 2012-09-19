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
class tubepress_impl_feed_UrlBuilderChainTest extends PHPUnit_Framework_TestCase
{
    private $_sut;

    private $_mockProviderCalculator;

    private $_mockChain;

    function setUp()
    {
        $this->_mockChain              = Mockery::mock('ehough_chaingang_api_Chain');
        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator($this->_mockProviderCalculator);

        $this->_sut                    = new tubepress_impl_feed_UrlBuilderChain($this->_mockChain);
    }

    public function tearDown()
    {
        Mockery::close();
    }

    /**
     * @expectedException InvalidArgumentException
     */
    function testBuildGalleryUrlNoCommandsCanHandle()
    {
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('providerName');

        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::on(function ($context) {

                return $context->get(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_PROVIDER_NAME) === 'providerName'
                    && $context->get(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_ARGUMENT) === 1
                    && $context->get(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_IS_SINGLE) === false;
            }
        ))->andReturn(false);

        $this->_sut->buildGalleryUrl(1);
    }

    function testBuildSingleVideoUrl()
    {
        $this->_mockProviderCalculator->shouldReceive('calculateProviderOfVideoId')->once()->with('video-id')->andReturn('providerName');

        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::on(function ($context) {

                $context->put(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_URL, 'stuff');

                return $context->get(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_PROVIDER_NAME) === 'providerName'
                    && $context->get(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_ARGUMENT) === 'video-id'
                    && $context->get(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_IS_SINGLE) === true;
            }
        ))->andReturn(true);

        $this->assertEquals('stuff', $this->_sut->buildSingleVideoUrl('video-id'));
    }

    function testBuildGalleryUrl()
    {
        $this->_mockProviderCalculator->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('providerName');

        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::on(function ($context) {

                $context->put(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_URL, 'stuff');

                return $context->get(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_PROVIDER_NAME) === 'providerName'
                    && $context->get(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_ARGUMENT) === 2
                    && $context->get(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_IS_SINGLE) === false;
            }
        ))->andReturn(true);

        $this->assertEquals('stuff', $this->_sut->buildGalleryUrl(2));
    }
}