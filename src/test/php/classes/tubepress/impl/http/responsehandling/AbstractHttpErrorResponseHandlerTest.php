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
abstract class tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandlerTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockContext;

    private $_mockResponse;

    function onSetup()
    {
        $this->_sut = $this->buildSut();

        $this->_mockResponse = new ehough_shortstop_api_HttpResponse();
        $this->_mockContext  = new ehough_chaingang_impl_StandardContext();
        $this->_mockContext->put(ehough_shortstop_impl_HttpResponseHandlerChain::CHAIN_KEY_RESPONSE, $this->_mockResponse);
    }

    function testWrongProvider()
    {
        $this->assertFalse($this->_sut->execute($this->_mockContext));
    }

    protected function getMessage()
    {
        $result = $this->_sut->execute($this->_mockContext);

        $this->assertTrue($result);

        $message = $this->_mockContext->get(ehough_shortstop_impl_HttpResponseHandlerChain::CHAIN_KEY_ERROR_MESSAGE);

        $this->assertNotNull($message);

        return $message;
    }

    protected abstract function buildSut();

    protected function getContext()
    {
        return $this->_mockContext;
    }

    protected function getResponse()
    {
        return $this->_mockResponse;
    }

    protected function getSut()
    {
        return $this->_sut;
    }

    protected function _setMessageBody($message)
    {
        $entity = new ehough_shortstop_api_HttpEntity();
        $entity->setContent($message);

        $this->_mockResponse->setEntity($entity);
    }
}

