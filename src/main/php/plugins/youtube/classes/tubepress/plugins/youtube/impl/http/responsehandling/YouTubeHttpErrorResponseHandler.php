<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Handles errors from YouTube.
 */
class tubepress_plugins_youtube_impl_http_responsehandling_YouTubeHttpErrorResponseHandler extends tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandler
{
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('YouTube Error Handler');
    }

    /**
     * Get a user-friendly response message for this HTTP response.
     *
     * @param ehough_shortstop_api_HttpResponse $response The HTTP response.
     *
     * @return string A user-friendly response message for this HTTP response.
     */
    protected final function getMessageForResponse(ehough_shortstop_api_HttpResponse $response)
    {
        $toReturn = '';

        switch ($response->getStatusCode()) {

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

        return $toReturn;
    }

    /**
     * Get the name of the provider that this command handles.
     *
     * @return string youtube|vimeo
     */
    protected final function getProviderName()
    {
        return 'youtube';
    }

    /**
     * Get a logging friendly name for this handler.
     *
     * @return string A logging friendly name for this handler.
     */
    protected final function getFriendlyProviderName()
    {
        return 'YouTube';
    }

    /**
     * Gets the logger.
     *
     * @return mixed ehough_epilog_api_ILogger
     */
    protected final function getLogger()
    {
        return $this->_logger;
    }

    protected final function canHandleResponse(ehough_shortstop_api_HttpResponse $response)
    {
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