<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListener
 */
class tubepress_test_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListener
     */
    private $_sut;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListener();
    }

    public function testMalformedMessage()
    {
        $this->setExpectedException('ehough_shortstop_api_exception_RuntimeException', 'Vimeo responded to TubePress with an HTTP 400');


        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent('O:8:"stdClas:12:"generated0.0717;s:4:"stat";s:4:"fail";s:3:"err";O:8:"stdClass":2:{s:4:"code";i:1;s:3:"msg";s:14:"==";}}');

        $response = new ehough_shortstop_api_HttpResponse();
        $response->setStatusCode(400);
        $response->setEntity($entity);

        $request = new ehough_shortstop_api_HttpRequest('GET', 'http://gdata.vimeo.com/some/thing');

        $event = new ehough_tickertape_GenericEvent($response);
        $event->setArgument('request', $request);

        $this->_sut->onResponse($event);
    }

    public function testNonVimeo()
    {
        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent("<errors xmlns='http://schemas.google.com/g/2005'><error><domain>GData</domain><code>InvalidRequestUriException</code><internalReason>foobar</internalReason></error></errors>");

        $response = new ehough_shortstop_api_HttpResponse();
        $response->setStatusCode(400);
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
        $this->setExpectedException('ehough_shortstop_api_exception_RuntimeException', 'Vimeo responded to TubePress with an HTTP 400 - yowza');

        $event = $this->_buildEvent(400, 'yowza');

        $this->_sut->onResponse($event);
    }

    public function _buildEvent($code, $message)
    {
        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent('O:8:"stdClass":3:{s:12:"generated_in";d:0.0717;s:4:"stat";s:4:"fail";s:3:"err";O:8:"stdClass":2:{s:4:"code";i:1;s:3:"msg";s:5:"' . $message . '";}}');

        $response = new ehough_shortstop_api_HttpResponse();
        $response->setStatusCode($code);
        $response->setEntity($entity);

        $request = new ehough_shortstop_api_HttpRequest('GET', 'http://api.vimeo.com/some/thing');

        $event = new ehough_tickertape_GenericEvent($response);
        $event->setArgument('request', $request);

        return $event;
    }
}

