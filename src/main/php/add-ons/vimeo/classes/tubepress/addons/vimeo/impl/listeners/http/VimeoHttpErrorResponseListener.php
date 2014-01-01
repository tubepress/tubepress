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
 * Handles errors from Vimeo.
 */
class tubepress_addons_vimeo_impl_listeners_http_VimeoHttpErrorResponseListener
{
    /**
     * @var ehough_epilog_Logger
     */
    private $_logger;

    public function __construct()
    {
        $this->_logger = ehough_epilog_LoggerFactory::getLogger('Vimeo Error Handler');
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

            //this is not a Vimeo response
            return;
        }

        switch ($response->getStatusCode()) {

            case 200:

                return;

            default:

                $toReturn = 'Vimeo responded to TubePress with an HTTP ' . $response->getStatusCode();
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

        return tubepress_impl_util_StringUtils::endsWith($host, 'vimeo.com');
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
        $entity       = $response->getEntity();
        $rawResponse  = $entity->getContent();
        $unserialized = @unserialize($rawResponse);

        return $unserialized->err->msg;
    }
}