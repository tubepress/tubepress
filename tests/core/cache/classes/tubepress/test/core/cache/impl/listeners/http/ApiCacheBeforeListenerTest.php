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
 * @covers tubepress_core_cache_impl_listeners_http_ApiCacheBeforeListener<extended>
 */
class tubepress_test_core_cache_impl_listeners_http_ApiCacheBeforeListenerTest extends tubepress_test_core_cache_impl_listeners_http_AbstractApiCacheListenerTest
{
    protected function buildSut()
    {
        return new tubepress_core_cache_impl_listeners_http_ApiCacheBeforeListener(

            $this->getMockLogger(),
            $this->getMockContext(),
            $this->getMockApiCache()
        );
    }

    public function testHit()
    {
        $this->setupForExecution();

        $this->getMockLogger()->shouldReceive('debug')->once()->with('Asking cache for <a href="<url>">URL</a>');
        $this->getMockLogger()->shouldReceive('debug')->once()->with('Cache hit for <a href="<url>">URL</a>.');
        $this->getMockLogger()->shouldReceive('debug')->once()->with('Cached result for <a href="<url>">URL</a> is in the HTML source for this page. <span style="display:none">abc</span>');
        $this->getMockApiCache()->shouldReceive('getItem')->once()->with('<url>')->andReturn($this->getMockCacheItem());
        $this->getMockCacheItem()->shouldReceive('get')->twice()->andReturn('abc');
        $this->getMockCacheItem()->shouldReceive('isMiss')->twice()->andReturn(false);

        $this->getMockEvent()->shouldReceive('setArgument')->once()->with('response', ehough_mockery_Mockery::on(function ($response) {

            return $response instanceof tubepress_core_http_api_message_ResponseInterface;
        }));
        $this->getMockEvent()->shouldReceive('stopPropagation')->once();

        $this->doRunTest();
    }

    public function testMiss()
    {
        $this->setupForExecution();

        $this->getMockLogger()->shouldReceive('debug')->once()->with('Asking cache for <a href="<url>">URL</a>');
        $this->getMockLogger()->shouldReceive('debug')->once()->with('Cache miss for <a href="<url>">URL</a>.');
        $this->getMockApiCache()->shouldReceive('getItem')->once()->with('<url>')->andReturn($this->getMockCacheItem());
        $this->getMockCacheItem()->shouldReceive('isMiss')->twice()->andReturn(true);

        $this->doRunTest();
    }

    protected function getSubject()
    {
        return $this->getMockRequest();
    }
}