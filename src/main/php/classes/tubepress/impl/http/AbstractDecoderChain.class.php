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
    'org_tubepress_api_http_HttpMessage',
    'org_tubepress_api_http_HttpResponse',
    'org_tubepress_spi_patterns_cor_Chain',
));

/**
 * Decodes HTTP messages using chain-of-responsibility.
 */
abstract class org_tubepress_impl_http_AbstractDecoderChain
{
    /**
     * Decodes transfer encoded data in the entity body of this response and re-assigns
     * the decoded entity to the response.
     *
     * @param org_tubepress_api_http_HttpResponse $response The HTTP response.
     *
     * @return void
     */
    function decode(org_tubepress_api_http_HttpResponse $response)
    {
        $ioc               = org_tubepress_impl_ioc_IocContainer::getInstance();
        $commands          = $this->getArrayOfCommandNames();
        $chain             = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $context           = $chain->createContextInstance();
        $context->response = $response;
        $status            = $chain->execute($context, $commands);

        if ($status === false) {

            throw new Exception('Unable to decode HTTP response');
        }

        $entity = $response->getEntity();
        $entity->setContent($context->decoded);
        $entity->setContentLength(strlen($context->decoded));

        $contentType = $response->getHeaderValue(org_tubepress_api_http_HttpMessage::HTTP_HEADER_CONTENT_TYPE);
        if ($contentType !== null) {

            $entity->setContentType($contentType);
        }

        $response->setEntity($entity);
    }

    /**
     * Determines if this message needs to be decoded.
     *
     * @param org_tubepress_api_http_HttpResponse $response The HTTP response.
     *
     * @return boolean True if this response should be decoded. False otherwise.
     */
    function needsToBeDecoded(org_tubepress_api_http_HttpResponse $response)
    {
        $entity = $response->getEntity();

        if ($entity === null) {

            org_tubepress_impl_log_Log::log($this->getLogPrefix(), 'Response contains no entity');
            return false;
        }

        $content = $entity->getContent();

        if ($content == '' || $content == null) {

            org_tubepress_impl_log_Log::log($this->getLogPrefix(), 'Response entity contains no content');
            return false;
        }

        $expectedHeaderName = $this->getHeaderName();
        $actualHeaderValue  = $response->getHeaderValue($expectedHeaderName);

        if ($actualHeaderValue === null) {

            org_tubepress_impl_log_Log::log($this->getLogPrefix(), 'Response does not contain %s header. No need to decode.', $expectedHeaderName);
            return false;
        }

        org_tubepress_impl_log_Log::log($this->getLogPrefix(), 'Response contains %s header. Will attempt decode.', $expectedHeaderName);
        return true;
    }

    protected abstract function getArrayOfCommandNames();

    protected abstract function getLogPrefix();

    protected abstract function getHeaderName();
}