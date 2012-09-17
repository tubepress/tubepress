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
class tubepress_impl_feed_CacheAwareFeedFetcherTest extends PHPUnit_Framework_TestCase
{
	private $_sut;

    private $_mockCache;

    private $_mockHttpClient;

    private $_mockHttpResponseHandler;

	function setUp()
	{
        $this->_mockCache      = Mockery::mock('ehough_stash_api_Cache');
        $this->_mockHttpClient = Mockery::mock('ehough_shortstop_api_HttpClient');
        $this->_mockHttpResponseHandler = Mockery::mock('ehough_shortstop_api_HttpResponseHandler');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setCacheService($this->_mockCache);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpClient($this->_mockHttpClient);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpResponseHandler($this->_mockHttpResponseHandler);

		$this->_sut            = new tubepress_impl_feed_CacheAwareFeedFetcher();
	}

	function testFetchGoodXmlCacheHit()
	{
	    $this->_mockCache->shouldReceive('get')->once()->with("http://www.ietf.org/css/ietf.css")->andReturn('someValue');

	    $this->assertEquals('someValue', $this->_sut->fetch("http://www.ietf.org/css/ietf.css", true));
	}

	function testFetchGoodXmlCacheMiss()
	{

        $this->_mockCache->shouldReceive('get')->once()->with("http://www.ietf.org/css/ietf.css")->andReturn(false);
        $this->_mockCache->shouldReceive('save')->once()->with("http://www.ietf.org/css/ietf.css", "someValue");

	    $this->_mockHttpClient->shouldReceive('executeAndHandleResponse')->once()->andReturn('someValue');

	    $this->assertEquals('someValue', $this->_sut->fetch("http://www.ietf.org/css/ietf.css", true));
	}

	function testFetchGoodXmlCacheDisabled()
	{
        $this->_mockHttpClient->shouldReceive('executeAndHandleResponse')->once()->andReturn('someValue');

		$this->assertEquals('someValue', $this->_sut->fetch("http://www.ietf.org/css/ietf.css", false));
	}
}
