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
class tubepress_impl_feed_CacheAwareFeedFetcherTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockCache;

    private $_mockHttpClient;

    private $_mockHttpResponseHandler;

	function onSetup()
	{
        $this->_mockCache               = $this->createMockSingletonService('ehough_stash_api_Cache');
        $this->_mockHttpClient          = $this->createMockSingletonService('ehough_shortstop_api_HttpClient');
        $this->_mockHttpResponseHandler = $this->createMockSingletonService('ehough_shortstop_api_HttpResponseHandler');

		$this->_sut = new tubepress_impl_feed_CacheAwareFeedFetcher();
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
