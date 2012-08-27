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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_spi_http_HttpMessageParser',
    'org_tubepress_api_http_HttpRequest',
    'org_tubepress_api_http_HttpResponse',
    'org_tubepress_spi_patterns_cor_Command',
    'org_tubepress_spi_http_HttpTransport',
));

/**
 * Lifted from http://core.trac.wordpress.org/browser/tags/3.0.4/wp-includes/class-http.php
 *
 * Base HTTP command.
 */
abstract class org_tubepress_impl_http_transports_AbstractHttpTransport implements org_tubepress_spi_patterns_cor_Command, org_tubepress_spi_http_HttpTransport
{
    /**
     * Execute the command.
     *
     * @param array $context An array of context elements (may be empty).
     *
     * @return boolean True if this command was able to handle the execution. False otherwise.
     */
    public function execute($context)
    {
        /* this will never be null if the parent chain does its job */
        $request = $context->request;

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Seeing if able to handle %s', $request);

        if ($this->isAvailable() === false || $this->canHandle($context->request) === false) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'Declined to handle %s', $request);
            return false;
        }

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Offered to handle %s. Now initializing.', $request);

        try {

            $context->response = $this->handle($request);

            return true;

        } catch (Exception $e) {

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'Caught exception when handling %s (%s). Will re-throw after tear down.', $request, $e->getMessage());
            $this->tearDown();
            throw $e;
        }
    }

    /**
     * Execute the given HTTP request.
     *
     * @param org_tubepress_api_http_HttpRequest $request The request to execute.
     *
     * @return org_tubepress_api_http_HttpResponse The HTTP response.
     */
    public function handle(org_tubepress_api_http_HttpRequest $request)
    {
        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Preparing to handle %s', $request);

        /** allow for setup */
        $this->prepareToHandleNewRequest($request);

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Now handling %s', $request);

        /** handle the request. */
        $rawResponse = $this->handleRequest($request);

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Assembling response from %s', $request);

        $response = $this->_buildResponse($rawResponse, $request);

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Tearing down after %s', $request);

        $this->tearDown();

        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Successfully handled %s', $request);

        return $response;
    }

    /**
     * Perform handling of the given request.
     *
     * @param org_tubepress_api_http_HttpRequest $request The HTTP request.
     *
     * @return string The raw response for this request. May be empty or null.
     */
    protected abstract function handleRequest(org_tubepress_api_http_HttpRequest $request);

    /**
     * Get the name of this transport.
     *
     * @return string The name of this transport.
     */
    protected abstract function getTransportName();

    /**
     * Get the response code.
     *
     * @return int the HTTP response code.
     */
    protected abstract function getResponseCode();

    /**
     * Perform optional setup to handle a new HTTP request.
     *
     * @param org_tubepress_api_http_HttpRequest $request The HTTP request to handle.
     *
     * @return void
     */
    protected function prepareToHandleNewRequest(org_tubepress_api_http_HttpRequest $request)
    {
        //override point
    }

   /**
    * Perform optional tear down after handling a request.
    *
    * @return void
    */
    protected function tearDown()
    {
        //override point
    }

    protected function logPrefix()
    {
    	return $this->getTransportName() . ' Transport';
    }

    private function _buildResponse($rawResponse, org_tubepress_api_http_HttpRequest $request)
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $hmp = $ioc->get(org_tubepress_spi_http_HttpMessageParser::_);

        /* first separate the headers from the body */
        $headersString = $hmp->getHeadersStringFromRawHttpMessage($rawResponse);
        if (empty($headersString)) {

            throw new Exception('Could not parse headers from response');
        }

        /* grab the body (may be empty) */
        $bodyString = $hmp->getBodyStringFromRawHttpMessage($rawResponse);

        /* make an array from the headers (may be empty) */
        $headers = $hmp->getArrayOfHeadersFromRawHeaderString($headersString);

        /* create a new response. */
        $response = new org_tubepress_api_http_HttpResponse();

        $this->_assignStatusToResponse($response, $request);
        $this->_assignHeadersToResponse($headers, $response, $request);
        $this->_assignEntityToResponse($bodyString, $response, $request);

        return $response;
    }

    private function _assignStatusToResponse(org_tubepress_api_http_HttpResponse $response, org_tubepress_api_http_HttpRequest $request)
    {
        $code = $this->getResponseCode();

        org_tubepress_impl_log_Log::log($this->logPrefix(), '%s returned HTTP %s', $request, $code);

        $response->setStatusCode($code);
    }

    private function _assignHeadersToResponse($headerArray, org_tubepress_api_http_HttpResponse $response, org_tubepress_api_http_HttpRequest $request)
    {
        if (! is_array($headerArray) || empty($headerArray)) {

            throw new Exception(sprintf('No headers in response from %s', $request));
        }

        foreach ($headerArray as $name => $value) {

            if (is_array($value)) {

                $value = implode(', ', $value);
            }

            $response->setHeader($name, $value);
        }

        /* do some logging */
        if (org_tubepress_impl_log_Log::isEnabled()) {

            $headerArray = $response->getAllHeaders();

            org_tubepress_impl_log_Log::log($this->logPrefix(), 'Here are the ' . count($headerArray) . ' headers in the response for %s', $request);

            foreach($headerArray as $name => $value) {

                org_tubepress_impl_log_Log::log($this->logPrefix(), "<tt>$name: $value</tt>");
            }
        }
    }

    private function _assignEntityToResponse($body, org_tubepress_api_http_HttpResponse $response, org_tubepress_api_http_HttpRequest $request)
    {
        org_tubepress_impl_log_Log::log($this->logPrefix(), 'Assigning (possibly empty) entity to response');

        $entity = new org_tubepress_api_http_HttpEntity();
        $entity->setContent($body);
        $entity->setContentLength(strlen($body));

        $contentType = $response->getHeaderValue(org_tubepress_api_http_HttpResponse::HTTP_HEADER_CONTENT_TYPE);
        if ($contentType !== null) {

            $entity->setContentType($contentType);
        }

        $response->setEntity($entity);
    }
}