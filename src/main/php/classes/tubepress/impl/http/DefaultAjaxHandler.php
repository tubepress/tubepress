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
 * Class for managing HTTP Transports and making HTTP requests.
 */
class tubepress_impl_http_DefaultAjaxHandler implements tubepress_spi_http_AjaxHandler
{
    /**
     * @var ehough_epilog_api_ILogger Logger.
     */
    private $_logger;

    /**
     * @var boolean Is debugging enabled?
     */
    private $_isDebugEnabled;

    public function __construct()
    {
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Default Ajax Handler');
    }

    /**
     * Handles incoming requests.
     *
     * @return void Handle the request and output a response.
     */
    public final function handle()
    {
        $this->_isDebugEnabled = $this->_logger->isDebugEnabled();

        if ($this->_isDebugEnabled) {

            $this->_logger->debug('Handling incoming request');
        }

        $httpRequestParameterService = tubepress_impl_patterns_sl_ServiceLocator::getHttpRequestParameterService();
        $actionName                  = $httpRequestParameterService->getParamValue(tubepress_spi_const_http_ParamName::ACTION);

        if ($actionName == '') {

            http_response_code(400);
            echo 'Missing "action" parameter';
            return;
        }

        $commandHandlers      = tubepress_impl_patterns_sl_ServiceLocator::getAjaxCommandHandlers();
        $chosenCommandHandler = null;

        if ($this->_isDebugEnabled) {

            $this->_logger->debug('There are ' . count($commandHandlers) . ' pluggable Ajax command service(s) registered');
        }

        foreach ($commandHandlers as $commandHandler) {

            /** @noinspection PhpUndefinedMethodInspection */
            if ($commandHandler->getName() === $actionName) {

                $chosenCommandHandler = $commandHandler;

                break;
            }

            if ($this->_isDebugEnabled) {

                $this->_logger->debug($commandHandler->getName() . ' could not handle action ' . $actionName);
            }
        }

        if ($chosenCommandHandler === null) {

            if ($this->_isDebugEnabled) {

                $this->_logger->debug('No pluggable Ajax command services could handle action ' . $actionName);
            }

            http_response_code(500);

            return;
        }

        if ($this->_isDebugEnabled) {

            $this->_logger->debug($chosenCommandHandler->getName() . ' chose to handle action ' . $actionName);
        }

        /** @noinspection PhpUndefinedMethodInspection */
        $chosenCommandHandler->handle();

        /** @noinspection PhpUndefinedMethodInspection */
        http_response_code($chosenCommandHandler->getHttpStatusCode());

        /** @noinspection PhpUndefinedMethodInspection */
        echo $chosenCommandHandler->getOutput();
    }

    public static function simulatedHttpResponseCode($code = null)
    {
        if ($code !== NULL) {

            switch ($code) {
                case 100: $text = 'Continue'; break;
                case 101: $text = 'Switching Protocols'; break;
                case 200: $text = 'OK'; break;
                case 201: $text = 'Created'; break;
                case 202: $text = 'Accepted'; break;
                case 203: $text = 'Non-Authoritative Information'; break;
                case 204: $text = 'No Content'; break;
                case 205: $text = 'Reset Content'; break;
                case 206: $text = 'Partial Content'; break;
                case 300: $text = 'Multiple Choices'; break;
                case 301: $text = 'Moved Permanently'; break;
                case 302: $text = 'Moved Temporarily'; break;
                case 303: $text = 'See Other'; break;
                case 304: $text = 'Not Modified'; break;
                case 305: $text = 'Use Proxy'; break;
                case 400: $text = 'Bad Request'; break;
                case 401: $text = 'Unauthorized'; break;
                case 402: $text = 'Payment Required'; break;
                case 403: $text = 'Forbidden'; break;
                case 404: $text = 'Not Found'; break;
                case 405: $text = 'Method Not Allowed'; break;
                case 406: $text = 'Not Acceptable'; break;
                case 407: $text = 'Proxy Authentication Required'; break;
                case 408: $text = 'Request Time-out'; break;
                case 409: $text = 'Conflict'; break;
                case 410: $text = 'Gone'; break;
                case 411: $text = 'Length Required'; break;
                case 412: $text = 'Precondition Failed'; break;
                case 413: $text = 'Request Entity Too Large'; break;
                case 414: $text = 'Request-URI Too Large'; break;
                case 415: $text = 'Unsupported Media Type'; break;
                case 500: $text = 'Internal Server Error'; break;
                case 501: $text = 'Not Implemented'; break;
                case 502: $text = 'Bad Gateway'; break;
                case 503: $text = 'Service Unavailable'; break;
                case 504: $text = 'Gateway Time-out'; break;
                case 505: $text = 'HTTP Version not supported'; break;

                default:
                    exit('Unknown http status code "' . htmlentities($code) . '"');
                    break;
            }

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0');

            header($protocol . ' ' . $code . ' ' . $text);

            $GLOBALS['http_response_code'] = $code;

        } else {

            $code = (isset($GLOBALS['http_response_code']) ? $GLOBALS['http_response_code'] : 200);

        }

        return $code;
    }
}

if (!function_exists('http_response_code')) {

    function http_response_code($code = null)
    {
        return tubepress_impl_http_DefaultAjaxHandler::simulatedHttpResponseCode($code);
    }
}
