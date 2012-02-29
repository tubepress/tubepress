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
    'org_tubepress_api_http_HttpResponseHandler',
));

/**
 * Handles HTTP responses.
 */
class org_tubepress_impl_http_HttpResponseHandlerChain implements org_tubepress_api_http_HttpResponseHandler
{
    private static $_logPrefix = 'HTTP Reponse Handler Chain';

    /**
     * Handles an HTTP response.
     *
     * @param org_tubepress_api_http_HttpResponse $response The HTTP response.
     *
     * @throws Exception If something goes wrong.
     *
     * @return string The raw entity body of the response. May be empty or null.
     */
    function handle(org_tubepress_api_http_HttpResponse $response)
    {
        $statusCode = $response->getStatusCode();

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Response returned status %d', $statusCode);

        switch ($statusCode) {

            case 200:

                return $this->_handleSuccess($response);

            default:

                return $this->_handleError($response);
        }
    }

    private function _handleError(org_tubepress_api_http_HttpResponse $response)
    {
        $ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
        $chain    = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $context  = $chain->createContextInstance();
        $commands = array(

            'org_tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandler'
        );

        $context->response = $response;

        $result = $chain->execute($context, $commands);

        if ($result !== true) {

            throw new Exception('An unknown HTTP error occurred. Please examine TubePress\'s debug output for further details');
        }

        throw new Exception($context->messageToDisplay);
    }

    private function _handleSuccess(org_tubepress_api_http_HttpResponse $response)
    {
        $entity = $response->getEntity();

        if ($entity !== null) {

            return $entity->getContent();
        }

        org_tubepress_impl_log_Log::log(self::$_logPrefix, 'Null entity in response');
        return '';
    }
}

