<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandlerTest extends tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandlerTest
{
    protected function buildSut()
    {
        return new tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandler();
    }

    protected function getProviderName()
    {
        return 'youtube';
    }

    function testErrorInternalReason()
    {
        $this->assertEquals('foobar', $this->_getMessageFromBody('some stuff <InTernALReAsON>foobar</inTERNalREasoN> hello'));
    }

    function testErrorTitle()
    {
        $this->assertEquals('foobar', $this->_getMessageFromBody('some stuff <tiTlE>foobar</TItLe> hello'));
    }

    function _getMessageFromBody($data)
    {
        $this->getResponse()->setStatusCode(200);

        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent($data);

        $this->getResponse()->setEntity($entity);

        return $this->getMessage();
    }

    function test503()
    {
        $this->_setMessageBody('<internalReason>something</internalReason>');

        $this->getResponse()->setStatusCode(503);

        $this->assertEquals('YouTube\'s API cannot be reached at this time (likely due to overload or maintenance). Please try again later.', $this->getMessage());
    }

    function test403()
    {
        $this->_setMessageBody('<internalReason>something</internalReason>');

        $this->getResponse()->setStatusCode(403);

        $this->assertEquals('YouTube determined that the request did not contain proper authentication.', $this->getMessage());
    }

    function test500()
    {
        $this->_setMessageBody('<internalReason>something</internalReason>');

        $this->getResponse()->setStatusCode(500);

        $this->assertEquals('YouTube experienced an internal error while handling this request. Please try again later.', $this->getMessage());
    }

    function test501()
    {
        $this->_setMessageBody('<internalReason>something</internalReason>');

        $this->getResponse()->setStatusCode(501);

        $this->assertEquals('The YouTube API does not implement the requested operation.', $this->getMessage());
    }

    function test401()
    {
        $this->_setMessageBody('<internalReason>something</internalReason>');

        $this->getResponse()->setStatusCode(401);

        $this->assertEquals('YouTube didn\'t authorize this request due to a missing or invalid Authorization header.', $this->getMessage());
    }

    function testNoEntity()
    {
        $this->getResponse()->setStatusCode(200);

        $this->assertFalse($this->getSut()->execute($this->getContext()));
    }
}

