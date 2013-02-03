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
class tubepress_plugins_youtube_impl_http_responsehandling_YouTubeHttpErrorResponseHandlerTest extends tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandlerTest
{
    protected function buildSut()
    {
        return new tubepress_plugins_youtube_impl_http_responsehandling_YouTubeHttpErrorResponseHandler();
    }

    protected function getProviderName()
    {
        return 'youtube';
    }

    function test400()
    {
        $result = $this->_prepareMessage(400, 'yowza');

        $this->assertEquals('YouTube didn\'t like something about TubePress\'s request. - yowza', $result);
    }

    function testHttp530()
    {
        $result = $this->_prepareMessage(530, 'foobar');

        $this->assertEquals('YouTube responded to TubePress with an HTTP 530 - foobar', $result);
    }

    function test503()
    {
        $result = $this->_prepareMessage(503, 'xyz');

        $this->assertEquals('YouTube\'s API cannot be reached at this time (likely due to overload or maintenance). Please try again later. - xyz', $result);
    }

    function test403()
    {
        $result = $this->_prepareMessage(403, 'aabc');

        $this->assertEquals('YouTube determined that TubePress\'s request did not contain proper authentication. - aabc', $result);
    }

    function test500()
    {
        $result = $this->_prepareMessage(500, 'wookie');

        $this->assertEquals('YouTube experienced an internal error while handling TubePress\'s request. Please try again later. - wookie', $result);
    }

    function test501()
    {
        $result = $this->_prepareMessage(501, 'force');

        $this->assertEquals('The YouTube API does not implement the requested operation. - force', $result);
    }

    function test401()
    {
        $result = $this->_prepareMessage(401, 'trek');

        $this->assertEquals('YouTube didn\'t authorize TubePress\'s request. - trek', $result);
    }

    function testNoEntity()
    {
        $this->getResponse()->setStatusCode(200);

        $this->assertFalse($this->getSut()->execute($this->getContext()));
    }

    function _prepareMessage($code, $message)
    {
        $this->getResponse()->setStatusCode($code);
        $this->getResponse()->setHeader('Content-Type', 'application/vnd.google.gdata.error+xml');

        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent("<errors xmlns='http://schemas.google.com/g/2005'><error><domain>GData</domain><code>InvalidRequestUriException</code><internalReason>$message</internalReason></error></errors>");

        $this->getResponse()->setEntity($entity);

        return $this->getMessage();
    }
}

