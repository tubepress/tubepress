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

class tubepress_http_impl_puzzle_RequestException extends tubepress_api_http_exception_RequestException
{
    /**
     * @var tubepress_api_http_message_RequestInterface
     */
    private $_request;

    /**
     * @var tubepress_api_http_message_ResponseInterface
     */
    private $_response;

    public function __construct(puzzle_exception_RequestException $delegate)
    {
        parent::__construct($delegate->getMessage(), $delegate->getCode());

        $delegateRequest = $delegate->getRequest();
        $this->_request  = $delegateRequest instanceof tubepress_api_http_message_RequestInterface ?
            $delegateRequest : new tubepress_http_impl_puzzle_PuzzleBasedRequest($delegateRequest);

        $delegateResponse = $delegate->getResponse();
        if ($delegateResponse !== null) {

            $this->_response = $delegateResponse instanceof tubepress_api_http_message_ResponseInterface ?
                $delegateResponse : new tubepress_http_impl_puzzle_PuzzleBasedResponse($delegateResponse);
        }
    }

    /**
     * Get the request that caused the exception.
     *
     * @return tubepress_api_http_message_RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get the associated response.
     *
     * @return tubepress_api_http_message_ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->_response;
    }
}
