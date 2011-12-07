<?php

require_once 'AbstractHttpErrorResponseHandlerTest.php';
require_once BASE . '/sys/classes/org/tubepress/impl/http/responsehandling/YouTubeHttpErrorResponseHandler.class.php';

class org_tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandlerTest extends org_tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandlerTest {

    protected function buildSut()
    {
        return new org_tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandler();
    }

    protected function getProviderName()
    {
        return org_tubepress_api_provider_Provider::YOUTUBE;
    }

    function testBadSyntax()
    {
        $this->assertEquals('YouTube rejected the request due to malformed syntax.', $this->_getMessageFromBody('some stuff <InTernALReAsONfoobar</inTERNalREasoN> hello'));
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
        $this->getResponse()->shouldReceive('getStatusCode')->once()->andReturn(200);
        $entity = \Mockery::mock(org_tubepress_api_http_HttpEntity::_);
        $entity->shouldReceive('getContent')->once()->andReturn($data);
        $this->getResponse()->shouldReceive('getEntity')->once()->andReturn($entity);

        return $this->getMessage();
    }

    function test503()
    {
        $this->getResponse()->shouldReceive('getStatusCode')->once()->andReturn(503);

        $this->assertEquals('YouTube\'s API cannot be reached at this time (likely due to overload or maintenance). Please try again later.', $this->getMessage());
    }

    function test403()
    {
        $this->getResponse()->shouldReceive('getStatusCode')->once()->andReturn(403);

        $this->assertEquals('YouTube determined that the request did not contain proper authentication.', $this->getMessage());
    }

    function test500()
    {
        $this->getResponse()->shouldReceive('getStatusCode')->once()->andReturn(500);

        $this->assertEquals('YouTube experienced an internal error while handling this request. Please try again later.', $this->getMessage());
    }

    function test501()
    {
        $this->getResponse()->shouldReceive('getStatusCode')->once()->andReturn(501);

        $this->assertEquals('The YouTube API does not implement the requested operation.', $this->getMessage());
    }

    function test401()
    {
        $this->getResponse()->shouldReceive('getStatusCode')->once()->andReturn(401);

        $this->assertEquals('YouTube didn\'t authorize this request due to a missing or invalid Authorization header.', $this->getMessage());
    }
}

