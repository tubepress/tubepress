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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../../impl/classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_provider_Provider',
    'org_tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandler',
));

/**
 * Handles errors from YouTube.
 */
class org_tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandler extends org_tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandler
{
    /**
     * Get a user-friendly response message for this HTTP response.
     *
     * @param org_tubepress_api_http_HttpResponse $response The HTTP response.
     *
     * @return string A user-friendly response message for this HTTP response.
     */
    protected function getMessageForResponse(org_tubepress_api_http_HttpResponse $response)
    {
        switch ($response->getStatusCode()) {

            case 401:

                return 'YouTube didn\'t authorize this request due to a missing or invalid Authorization header.';

            case 403:

                return 'YouTube determined that the request did not contain proper authentication.';

            case 500:

                return 'YouTube experienced an internal error while handling this request. Please try again later.';

            case 501:

                return 'The YouTube API does not implement the requested operation.';

            case 503:

                return 'YouTube\'s API cannot be reached at this time (likely due to overload or maintenance). Please try again later.';

            default:

                return $this->_parseError($response);
        }
    }

    private function _parseError(org_tubepress_api_http_HttpResponse $response)
    {
        $entity = $response->getEntity();

        if ($entity === null) {

            throw new Exception('Missing entity in response');
        }

        $rawResponse = $entity->getContent();

        preg_match('/.*<(?:title|internalreason)>([^<]+)<\/(?:title|internalreason)>.*/i', $rawResponse, $matches);

        if (count($matches) > 1) {

            return $matches[1];
        }

        return 'YouTube rejected the request due to malformed syntax.';
    }

    /**
     * Get the name of the provider that this command handles.
     *
     * @return string youtube|vimeo
     */
    protected function getProviderName()
    {
        return org_tubepress_api_provider_Provider::YOUTUBE;
    }

    /**
     * Get a logging friendly name for this handler.
     *
     * @return string A logging friendly name for this handler.
     */
    protected function getFriendlyProviderName()
    {
        return 'YouTube';
    }
}