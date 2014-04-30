<?php
/**
 * Copyright 2006 - 2013 Eric D. Hough (http://ehough.com)
 *
 * This file is part of coauthor (https://github.com/ehough/coauthor)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_puzzle_impl_exception_RequestException extends tubepress_spi_http_RequestException
{
    /**
     * @var tubepress_api_http_RequestInterface
     */
    private $_request;

    /**
     * @var tubepress_api_http_ResponseInterface
     */
    private $_response;

    public function __construct(puzzle_exception_RequestException $delegate)
    {
        parent::__construct($delegate->getMessage(), $delegate->getCode());

        $delegateRequest = $delegate->getRequest();
        $this->_request = $delegateRequest instanceof tubepress_api_http_RequestInterface ?
            $delegateRequest : new tubepress_addons_puzzle_impl_message_PuzzleBasedRequest($delegateRequest);

        $delegateResponse = $delegate->getResponse();
        if ($delegateResponse !== null) {

            $this->_response = $delegateResponse instanceof tubepress_api_http_ResponseInterface ?
                $delegateResponse : new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse($delegateResponse);
        }
    }

    /**
     * Get the request that caused the exception
     *
     * @return tubepress_api_http_RequestInterface
     */
    public function getRequest()
    {
        return $this->_request;
    }

    /**
     * Get the associated response
     *
     * @return tubepress_api_http_ResponseInterface|null
     */
    public function getResponse()
    {
        return $this->_response;
    }
}