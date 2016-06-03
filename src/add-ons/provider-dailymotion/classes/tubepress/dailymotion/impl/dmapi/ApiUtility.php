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
class tubepress_dailymotion_impl_dmapi_ApiUtility
{
    /**
     * @var tubepress_api_log_LoggerInterface
     */
    private $_logger;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    /**
     * @var tubepress_api_http_HttpClientInterface
     */
    private $_httpClient;

    /**
     * @var tubepress_api_array_ArrayReaderInterface
     */
    private $_arrayReader;

    /**
     * @var array
     */
    private $_memoryCache;

    /**
     * @var bool
     */
    private $_shouldLog;

    public function __construct(tubepress_api_log_LoggerInterface        $logger,
                                tubepress_api_options_ContextInterface   $context,
                                tubepress_api_http_HttpClientInterface   $httpClient,
                                tubepress_api_array_ArrayReaderInterface $arrayReader)
    {
        $this->_logger      = $logger;
        $this->_context     = $context;
        $this->_httpClient  = $httpClient;
        $this->_arrayReader = $arrayReader;
        $this->_memoryCache = array();
        $this->_shouldLog   = $logger->isEnabled();
    }

    /**
     * @param tubepress_api_url_UrlInterface $url
     * @param array                          $requestOpts
     *
     * @return array
     */
    public function getDecodedApiResponse(tubepress_api_url_UrlInterface $url, $requestOpts = array())
    {
        $httpRequest = $this->_httpClient->createRequest('GET', $url, $requestOpts);
        $finalConfig = array_merge($httpRequest->getConfig(), array('tubepress-remote-api-call' => true));

        $httpRequest->setConfig($finalConfig);

        $urlAsString = $url->toString();

        if (!isset($this->_memoryCache[$urlAsString])) {

            $httpResponse = $this->_httpClient->send($httpRequest);
            $rawFeed      = $httpResponse->getBody()->toString();
            $decoded      = json_decode($rawFeed, true);

            if ($decoded === null) {

                throw new RuntimeException('Unable to decode JSON from Dailymotion');
            }

            $this->checkForApiResponseError($decoded);

            $this->_memoryCache[$urlAsString] = $decoded;

        } else {

            if ($this->_shouldLog) {

                $this->_logger->debug(sprintf('Response for <code>%s</code> found in the in-memory cache.', $urlAsString));
            }
        }

        return $this->_memoryCache[$urlAsString];
    }

    public function checkForApiResponseError(array $json)
    {
        $errorMessage = $this->_arrayReader->getAsString($json, 'error.message');

        if ($errorMessage) {

            throw new RuntimeException(sprintf('Dailymotion responded with an error: %s',
                $errorMessage
            ));
        }
    }
}
