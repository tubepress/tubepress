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
class tubepress_addons_puzzle_impl_http_PuzzleHttpClient implements tubepress_spi_http_HttpClientInterface
{
    /**
     * @var puzzle_ClientInterface
     */
    private $_delegate;

    public function __construct(puzzle_ClientInterface $delegate)
    {
        $this->_delegate = $delegate;
    }

    /**
     * Create and return a new {@see tubepress_api_http_RequestInterface} object.
     *
     * Use an absolute path to override the base path of the client, or a
     * relative path to append to the base path of the client. The URL can
     * contain the query string as well.
     *
     * @param string                                $method  HTTP method
     * @param string|tubepress_api_url_UrlInterface $url     URL
     * @param array                                 $options Array of request options to apply.
     *
     * @return tubepress_api_http_RequestInterface
     */
    public function createRequest($method, $url = null, array $options = array())
    {
        $puzzleRequest = $this->_delegate->createRequest($method, "$url", $options);

        return new tubepress_addons_puzzle_impl_message_PuzzleBasedRequest($puzzleRequest);
    }

    /**
     * Send a GET request
     *
     * @param string|tubepress_api_url_UrlInterface $url     URL
     * @param array                                 $options Array of request options to apply.
     *
     * @return tubepress_api_http_ResponseInterface
     * @throws tubepress_spi_http_RequestException When an error is encountered
     */
    public function get($url = null, $options = array())
    {
        $request = $this->createRequest('GET', $url, $options);

        return $this->send($request);
    }

    /**
     * Get default request options of the client.
     *
     * @param string|null $keyOrPath The Path to a particular default request
     *                               option to retrieve or pass null to retrieve all default request
     *                               options. The syntax uses "/" to denote a path through nested PHP
     *                               arrays. For example, "headers/content-type".
     *
     * @return mixed
     */
    public function getDefaultOption($keyOrPath = null)
    {
        return $this->_delegate->getDefaultOption($keyOrPath);
    }

    /**
     * Sends a single request
     *
     * @param tubepress_api_http_RequestInterface $request Request to send
     *
     * @return tubepress_api_http_ResponseInterface
     * @throws LogicException When the underlying adapter does not populate a response
     * @throws tubepress_spi_http_RequestException When an error is encountered
     */
    public function send(tubepress_api_http_RequestInterface $request)
    {
        $puzzleRequest = new puzzle_message_Request(

            $request->getMethod(),
            $request->getUrl()->toString(),
            $request->getHeaders(),
            new tubepress_addons_puzzle_impl_stream_FlexibleStream($request->getBody()),
            $request->getConfig()
        );

        $puzzleResponse = null;

        try {

            $puzzleResponse = $this->_delegate->send($puzzleRequest);

        } catch (puzzle_exception_RequestException $e) {

            throw new tubepress_addons_puzzle_impl_exception_RequestException($e);
        }

        return new tubepress_addons_puzzle_impl_message_PuzzleBasedResponse($puzzleResponse);
    }

    /**
     * Set a default request option on the client so that any request created
     * by the client will use the provided default value unless overridden
     * explicitly when creating a request.
     *
     * @param string|null $keyOrPath The Path to a particular configuration
     *                               value to set. The syntax uses a path notation that allows you to
     *                               specify nested configuration values (e.g., 'headers/content-type').
     * @param mixed       $value     Default request option value to set
     */
    public function setDefaultOption($keyOrPath, $value)
    {
        $this->_delegate->setDefaultOption($keyOrPath, $value);
    }
}