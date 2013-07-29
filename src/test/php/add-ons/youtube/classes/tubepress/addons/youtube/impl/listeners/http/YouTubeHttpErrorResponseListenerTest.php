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
class tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListener
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListener();
    }

    public function testMalformedMessage()
    {
        $this->setExpectedException('ehough_shortstop_api_exception_RuntimeException', 'YouTube didn\'t like something about TubePress\'s request');


        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent("<errorror><domain>GData</domain><code>InvalidRequestUriException</code><interrors>");

        $response = new ehough_shortstop_api_HttpResponse();
        $response->setStatusCode(400);
        $response->setHeader('Content-Type', 'application/vnd.google.gdata.error+xml');
        $response->setEntity($entity);

        $request = new ehough_shortstop_api_HttpRequest('GET', 'http://gdata.youtube.com/some/thing');

        $event = new ehough_tickertape_GenericEvent($response);
        $event->setArgument('request', $request);

        $this->_sut->onResponse($event);
    }

    public function testNonYouTube()
    {
        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent("<errors xmlns='http://schemas.google.com/g/2005'><error><domain>GData</domain><code>InvalidRequestUriException</code><internalReason>foobar</internalReason></error></errors>");

        $response = new ehough_shortstop_api_HttpResponse();
        $response->setStatusCode(400);
        $response->setHeader('Content-Type', 'application/vnd.google.gdata.error+xml');
        $response->setEntity($entity);

        $request = new ehough_shortstop_api_HttpRequest('GET', 'http://gdata.somethingelse.com/some/thing');

        $event = new ehough_tickertape_GenericEvent($response);
        $event->setArgument('request', $request);

        $this->_sut->onResponse($event);

        $this->assertTrue(true);
    }

    public function test200()
    {
        $event = $this->_buildEvent(200, 'yowza');

        $this->_sut->onResponse($event);

        $this->assertTrue(true);
    }

    public function test400()
    {
        $this->setExpectedException('ehough_shortstop_api_exception_RuntimeException', 'YouTube didn\'t like something about TubePress\'s request. - yowza');

        $event = $this->_buildEvent(400, 'yowza');

        $this->_sut->onResponse($event);
    }

    public function testHttp530()
    {
        $this->setExpectedException('ehough_shortstop_api_exception_RuntimeException','YouTube responded to TubePress with an HTTP 530 - foobar');

        $event = $this->_buildEvent(530, 'foobar');

        $this->_sut->onResponse($event);
    }

    public function test503()
    {
        $this->setExpectedException('ehough_shortstop_api_exception_RuntimeException', 'YouTube\'s API cannot be reached at this time (likely due to overload or maintenance). Please try again later. - xyz');

        $event = $this->_buildEvent(503, 'xyz');

        $this->_sut->onResponse($event);
    }

    public function test403()
    {
        $this->setExpectedException('ehough_shortstop_api_exception_RuntimeException', 'YouTube determined that TubePress\'s request did not contain proper authentication. - aabc');

        $event = $this->_buildEvent(403, 'aabc');

        $this->_sut->onResponse($event);
    }

    public function test500()
    {
        $this->setExpectedException('ehough_shortstop_api_exception_RuntimeException', 'YouTube experienced an internal error while handling TubePress\'s request. Please try again later. - wookie');

        $event = $this->_buildEvent(500, 'wookie');

        $this->_sut->onResponse($event);
    }

    public function test501()
    {
        $this->setExpectedException('ehough_shortstop_api_exception_RuntimeException', 'The YouTube API does not implement the requested operation. - force');

        $event = $this->_buildEvent(501, 'force');

        $this->_sut->onResponse($event);
    }

    public function test401()
    {
        $this->setExpectedException('ehough_shortstop_api_exception_RuntimeException', 'YouTube didn\'t authorize TubePress\'s request. - trek');

        $event = $this->_buildEvent(401, 'trek');

        $this->_sut->onResponse($event);
    }

    public function _buildEvent($code, $message)
    {
        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent("<errors xmlns='http://schemas.google.com/g/2005'><error><domain>GData</domain><code>InvalidRequestUriException</code><internalReason>$message</internalReason></error></errors>");

        $response = new ehough_shortstop_api_HttpResponse();
        $response->setStatusCode($code);
        $response->setHeader('Content-Type', 'application/vnd.google.gdata.error+xml');
        $response->setEntity($entity);

        $request = new ehough_shortstop_api_HttpRequest('GET', 'http://gdata.youtube.com/some/thing');

        $event = new ehough_tickertape_GenericEvent($response);
        $event->setArgument('request', $request);

        return $event;
    }
}

