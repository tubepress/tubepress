<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_dailymotion_impl_dmapi_ApiUtility
 */
class tubepress_test_dailymotion_impl_dmapi_ApiUtilityTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_dailymotion_impl_dmapi_ApiUtility
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockLogger;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockHttpClient;

    public function onSetup()
    {
        $this->_mockLogger     = $this->mock(tubepress_api_log_LoggerInterface::_);
        $this->_mockContext    = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockHttpClient = $this->mock(tubepress_api_http_HttpClientInterface::_);
        $arrayReader           = new tubepress_array_impl_ArrayReader();

        $this->_mockLogger->shouldReceive('isEnabled')->atLeast(1)->andReturn(true);

        $this->_sut = new tubepress_dailymotion_impl_dmapi_ApiUtility(

            $this->_mockLogger,
            $this->_mockContext,
            $this->_mockHttpClient,
            $arrayReader
        );
    }

    /**
     * @dataProvider getDataDecodedResponse
     */
    public function testGetDecodedApiResponse($responseContents, $exceptionMessage, $expected)
    {
        $mockUrl      = $this->mock(tubepress_api_url_UrlInterface::_);
        $mockRequest  = $this->mock('tubepress_api_http_message_RequestInterface');
        $mockResponse = $this->mock('tubepress_api_http_message_ResponseInterface');
        $mockBody     = $this->mock('tubepress_api_streams_StreamInterface');

        $this->_mockHttpClient->shouldReceive('createRequest')->once()
            ->with('GET', $mockUrl, array('foo' => 'bar'))->andReturn($mockRequest);
        $this->_mockHttpClient->shouldReceive('send')->once()->with($mockRequest)->andReturn($mockResponse);

        $mockRequest->shouldReceive('getConfig')->once()->andReturn(array('initial' => 'config'));
        $mockRequest->shouldReceive('setConfig')->once()->with(array(
            'initial'                   => 'config',
            'tubepress-remote-api-call' => true,
        ));

        $mockUrl->shouldReceive('toString')->once()->andReturn('some url');

        $mockResponse->shouldReceive('getBody')->once()->andReturn($mockBody);
        $mockBody->shouldReceive('toString')->once()->andReturn($responseContents);

        if ($exceptionMessage) {

            $this->setExpectedException('RuntimeException', $exceptionMessage);
        }

        $actual = $this->_sut->getDecodedApiResponse($mockUrl, array('foo' => 'bar'));

        if ($expected) {

            $this->assertEquals($expected, $actual);
        }
    }

    public function getDataDecodedResponse()
    {
        return array(

            array(
                'non-json', 'Unable to decode JSON from Dailymotion', null,
            ),
            array(
                json_encode(array(
                    'error' => array(
                        'message' => 'hi!',
                    ),
                )), 'Dailymotion responded with an error: hi!', null,
            ),
            array(
                json_encode(array(
                    'dm' => 'response',
                )), null, array('dm' => 'response'),
            ),
        );
    }

    /**
     * @dataProvider getDataErrorResponses
     */
    public function testError(array $response, $msg)
    {
        if ($msg) {

            $this->setExpectedException('RuntimeException', $msg);
        }

        $this->_sut->checkForApiResponseError($response);
    }

    public function getDataErrorResponses()
    {
        return array(
            array(
                array(), null,
            ),
            array(
                array('error' => array('message' => 'hello')), 'Dailymotion responded with an error: hello',
            ),
        );
    }
}
