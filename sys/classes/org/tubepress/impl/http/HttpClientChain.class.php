<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Advanced',
    'org_tubepress_api_http_HttpClient',
    'org_tubepress_api_http_HttpRequest',
    'org_tubepress_spi_http_HttpContentDecoder',
    'org_tubepress_spi_http_HttpTransferDecoder',
    'org_tubepress_api_http_HttpResponseHandler',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_spi_patterns_cor_Chain',
    'org_tubepress_spi_http_HttpContentDecoder'
));

/**
 * Class for managing HTTP Transports and making HTTP requests.
 */
class org_tubepress_impl_http_HttpClientChain implements org_tubepress_api_http_HttpClient
{
    private static $_logPrefix = 'HTTP Client';

    /**
    * Execute a given HTTP request.
    *
    * @param org_tubepress_api_http_HttpRequest $request The HTTP request.
    *
    * @throws Exception If something goes wrong.
    *
    * @return org_tubepress_api_http_HttpResponse The HTTP response.
    */
    function execute(org_tubepress_api_http_HttpRequest $request)
    {
        self::_checkRequest($request);
        self::_setDefaultHeaders($request);

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Will attempt %s', $request);

        $this->_logRequest($request);

        $ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
        $commands = self::_getTransportCommands($ioc);
        $sm       = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $context  = $sm->createContextInstance();

        $context->request = $request;

        $status = $sm->execute($context, $commands);

        if ($status === false) {

            throw new Exception(sprintf('No HTTP transports could execute %s to %s', $request->getMethod(), $request->getUrl()));
        }

        $response = $context->response;

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Now decoding response (if required)');
        $transferDecoder = $ioc->get(org_tubepress_spi_http_HttpTransferDecoder::_);
        $contentDecoder  = $ioc->get(org_tubepress_spi_http_HttpContentDecoder::_);

        $this->_decode($transferDecoder, $response, 'Transfer');
        $this->_decode($contentDecoder, $response, 'Content');

        self::_logEntityContent($request, $response);

        return $response;
    }

    /**
     * Execute a given HTTP request.
     *
     * @param org_tubepress_api_http_HttpRequest         $request The HTTP request.
     * @param org_tubepress_api_http_HttpResponseHandler $handler The HTTP response handler.
     *
     * @throws Exception If something goes wrong.
     *
     * @return string The raw entity data in the response. May be empty or null.
     */
    public function executeAndHandleResponse(org_tubepress_api_http_HttpRequest $request, org_tubepress_api_http_HttpResponseHandler $handler)
    {
        $response = $this->execute($request);

        return $handler->handle($response);
    }

    private static function _logEntityContent(org_tubepress_api_http_HttpRequest $request, org_tubepress_api_http_HttpResponse $response)
    {
    	org_tubepress_impl_log_Log::log(self::$_logPrefix, 'The raw result for %s is in the HTML source for this page <span style="display:none">%s</span>',
    		$request, htmlspecialchars(var_export($response, true)));
    }

    private static function _checkRequest(org_tubepress_api_http_HttpRequest $request)
    {
        if ($request->getMethod() === null) {

            throw new Exception('Request has no method set');
        }

        if ($request->getUrl() === null) {

            throw new Exception('Request has no URL set');
        }
    }

    private static function _setDefaultHeaders(org_tubepress_api_http_HttpRequest $request)
    {
        self::_setDefaultHeadersBasic($request);
        self::_setDefaultHeadersCompression($request);
        self::_setDefaultHeadersContent($request);
    }

    private static function _setDefaultHeadersContent(org_tubepress_api_http_HttpRequest $request)
    {
        $entity = $request->getEntity();

        if ($entity === null) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'No HTTP entity in request');
            return;
        }

        $contentLength   = $entity->getContentLength();
        $contentEncoding = $entity->getContentEncoding();
        $content         = $entity->getContent();
        $type            = $entity->getContentType();

        if ($content !== null && $contentEncoding !== null && $contentLength !== null && $type !== null) {

            $request->setHeader(org_tubepress_api_http_HttpMessage::HTTP_HEADER_CONTENT_ENCODING, $contentEncoding);
            $request->setHeader(org_tubepress_api_http_HttpMessage::HTTP_HEADER_CONTENT_LENGTH, $contentLength);
            $request->setHeader(org_tubepress_api_http_HttpMessage::HTTP_HEADER_CONTENT_TYPE, $type);

            return;

        }

        if ($contentLength === null) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'HTTP entity in request, but no content length set. Ignoring this entity!');
        }

        if ($contentEncoding === null) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'HTTP entity in request, but no content encoding set. Ignoring this entity!');
        }

        if ($content === null) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'HTTP entity in request, but no content set. Ignoring this entity!');
        }

        if ($type === null) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'HTTP entity in request, but no content type set. Ignoring this entity!');
        }
    }

    private static function _setDefaultHeadersCompression(org_tubepress_api_http_HttpRequest $request)
    {
        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Determining if HTTP compression is available...');

        $ioc    = org_tubepress_impl_ioc_IocContainer::getInstance();
        $decomp = $ioc->get(org_tubepress_spi_http_HttpContentDecoder::_);
        $header = $decomp->getAcceptEncodingHeaderValue();

        if ($header !== null) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'HTTP decompression is available. Yeah!');

            $request->setHeader(org_tubepress_api_http_HttpRequest::HTTP_HEADER_ACCEPT_ENCODING, $header);

        } else {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'HTTP decompression is NOT available. Boo.');
        }
    }

    private static function _setDefaultHeadersBasic(org_tubepress_api_http_HttpRequest $request)
    {
        $map = array(

            /* set our User-Agent */
            org_tubepress_api_http_HttpRequest::HTTP_HEADER_USER_AGENT => 'TubePress; http://tubepress.org',

            /* set to HTTP 1.1 */
            org_tubepress_api_http_HttpMessage::HTTP_HEADER_HTTP_VERSION => 'HTTP/1.0'
        );

        foreach ($map as $headerName => $headerValue) {

            /* only set these headers if someone else hasn't already */
            if (! $request->containsHeader($headerName)) {

                $request->setHeader($headerName, $headerValue);
            }
        }
    }

    private static function _getTransportCommands(org_tubepress_api_ioc_IocService $ioc)
    {
        $result  = array();
        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $map     = array(

            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP   => 'org_tubepress_impl_http_transports_ExtHttpTransport',
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL      => 'org_tubepress_impl_http_transports_CurlTransport',
			org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS   => 'org_tubepress_impl_http_transports_StreamsTransport',
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN => 'org_tubepress_impl_http_transports_FsockOpenTransport',
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN     => 'org_tubepress_impl_http_transports_FopenTransport',
        );

        foreach ($map as $optionName => $transport) {

            /* use the transport if it's not disabled */
            if (!$context->get($optionName)) {

                $result[] = $transport;

            } else {

                org_tubepress_impl_log_Log::log(self::$_logPrefix, '%s has been selected', $optionName);
            }
        }

        return $result;
    }

    private function _decode($decoder, $response, $name)
    {
        if ($decoder->needsToBeDecoded($response)) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Response is %s-Encoded. Attempting decode.', $name);
            $decoder->decode($response);
            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Successfully decoded %s-Encoded response.', $name);

        } else {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Response is not %s-Encoded.', $name);
        }
    }

    private function _logRequest(org_tubepress_api_http_HttpRequest $request)
    {
        $headerArray = $request->getAllHeaders();

        /* do some logging */
        if (org_tubepress_impl_log_Log::isEnabled()) {

            org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Here are the ' . count($headerArray) . ' headers in the request for %s', $request);

            foreach($headerArray as $name => $value) {

                org_tubepress_impl_log_Log::log(self::$_logPrefix, "<tt>$name: $value</tt>");
            }
        }
    }
}

