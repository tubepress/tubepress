<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_core_cache_impl_listeners_http_ApiCacheAfterListener<extended>
 */
class tubepress_test_core_cache_impl_listeners_http_ApiCacheAfterListenerTest extends tubepress_test_core_cache_impl_listeners_http_AbstractApiCacheListenerTest
{
    protected function buildSut()
    {
        return new tubepress_core_cache_impl_listeners_http_ApiCacheAfterListener(

            $this->getMockLogger(),
            $this->getMockContext(),
            $this->getMockApiCache()
        );
    }

    public function testBadSave()
    {
        $this->setupForExecution();

        $this->getMockResponse()->shouldReceive('getBody')->once()->andReturn($this->getMockBody());
        $this->getMockBody()->shouldReceive('toString')->twice()->andReturn('abc');
        $this->getMockLogger()->shouldReceive('debug')->once()->with('Raw result for <a href="<url>">URL</a> is in the HTML source for this page. <span style="display:none">abc</span>');
        $this->getMockLogger()->shouldReceive('error')->once()->with('Unable to store data to cache');
        $this->getMockApiCache()->shouldReceive('getItem')->once()->with('<url>')->andReturn($this->getMockCacheItem());
        $this->getMockContext()->shouldReceive('get')->once()->with(tubepress_core_cache_api_Constants::LIFETIME_SECONDS)->andReturn(44);
        $this->getMockCacheItem()->shouldReceive('set')->once()->with('abc', 44)->andReturn(false);

        $this->getMockResponse()->shouldReceive('hasHeader')->once()->with('TubePress-API-Cache-Hit')->andReturn(false);

        $this->doRunTest();
    }

    public function testGoodSave()
    {
        $this->setupForExecution();

        $this->getMockResponse()->shouldReceive('getBody')->once()->andReturn($this->getMockBody());
        $this->getMockBody()->shouldReceive('toString')->twice()->andReturn('abc');
        $this->getMockLogger()->shouldReceive('debug')->once()->with('Raw result for <a href="<url>">URL</a> is in the HTML source for this page. <span style="display:none">abc</span>');
        $this->getMockApiCache()->shouldReceive('getItem')->once()->with('<url>')->andReturn($this->getMockCacheItem());
        $this->getMockContext()->shouldReceive('get')->once()->with(tubepress_core_cache_api_Constants::LIFETIME_SECONDS)->andReturn(44);
        $this->getMockCacheItem()->shouldReceive('set')->once()->with('abc', 44)->andReturn(true);

        $this->getMockResponse()->shouldReceive('hasHeader')->once()->with('TubePress-API-Cache-Hit')->andReturn(false);

        $this->doRunTest();
    }

    public function testCacheHit()
    {
        $this->setupForExecution();

        $this->getMockResponse()->shouldReceive('hasHeader')->once()->with('TubePress-API-Cache-Hit')->andReturn(true);

        $this->doRunTest();
    }

    protected function getSubject()
    {
        return $this->getMockResponse();
    }
}