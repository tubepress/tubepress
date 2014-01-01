<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Handles errors from YouTube.
 */
class tubepress_addons_youtube_impl_listeners_http_YouTubeHttpErrorResponseListener
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('YouTube Error Handler');
    }

    public final function onResponse(ehough_tickertape_GenericEvent $event)
    {
        /**
         * @var $request ehough_shortstop_api_HttpRequest
         */
        $request  = $event->getArgument('request');

        /**
         * @var $response ehough_shortstop_api_HttpResponse
         */
        $response = $event->getSubject();

        if (!$this->_canHandle($request, $response)) {

            //this is not a YouTube response
            return;
        }

        switch ($response->getStatusCode()) {

            case 200:

                return;

            case 400:

                $toReturn = 'YouTube didn\'t like something about TubePress\'s request.';
                break;

            case 401:

                $toReturn = 'YouTube didn\'t authorize TubePress\'s request.';
                break;

            case 403:

                $toReturn = 'YouTube determined that TubePress\'s request did not contain proper authentication.';
                break;

            case 500:

                $toReturn = 'YouTube experienced an internal error while handling TubePress\'s request. Please try again later.';
                break;

            case 501:

                $toReturn = 'The YouTube API does not implement the requested operation.';
                break;

            case 503:

                $toReturn = 'YouTube\'s API cannot be reached at this time (likely due to overload or maintenance). Please try again later.';
                break;

            default:

                $toReturn = 'YouTube responded to TubePress with an HTTP ' . $response->getStatusCode();
                break;
        }

        $parsedError = $this->_parseError($response);

        if ($parsedError) {

            $toReturn .= ' - ' . $parsedError;
        }

        throw new ehough_shortstop_api_exception_RuntimeException($toReturn);
    }

    private function _canHandle(ehough_shortstop_api_HttpRequest $request, ehough_shortstop_api_HttpResponse $response)
    {
        $url  = $request->getUrl();
        $host = $url->getHost();

        if (!tubepress_impl_util_StringUtils::endsWith($host, 'youtube.com')) {

            return false;
        }

        $contentType = $response->getHeaderValue('Content-Type');
        $entity      = $response->getEntity();

        return $entity && $contentType === 'application/vnd.google.gdata.error+xml';
    }

    private function _parseError(ehough_shortstop_api_HttpResponse $response)
    {
        try {

            return $this->_doParseError($response);

        } catch (Exception $e) {

            return '';
        }
    }

    private function _doParseError(ehough_shortstop_api_HttpResponse $response)
    {
        $entity      = $response->getEntity();
        $rawResponse = $entity->getContent();
        $domDocument = new DOMDocument();

        $domDocument->loadXML($rawResponse);

        $xpath = new DOMXPath($domDocument);
        $xpath->registerNamespace('google', 'http://schemas.google.com/g/2005');

        return $xpath->query('//google:error[1]/google:internalReason')->item(0)->nodeValue;
    }
}