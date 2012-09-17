<?php
/**
 * Copyright 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of shortstop (https://github.com/ehough/shortstop)
 *
 * shortstop is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * shortstop is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with shortstop.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Handles errors from YouTube.
 */
class tubepress_impl_http_responsehandling_YouTubeHttpErrorResponseHandler extends tubepress_impl_http_responsehandling_AbstractHttpErrorResponseHandler
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
    protected function getMessageForResponse(ehough_shortstop_api_HttpResponse $response)
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

    /**
     * Get the name of the provider that this command handles.
     *
     * @return string youtube|vimeo
     */
    protected function getProviderName()
    {
        return tubepress_spi_provider_Provider::YOUTUBE;
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

    /**
     * Gets the logger.
     *
     * @return mixed ehough_epilog_api_ILogger
     */
    protected function getLogger()
    {
        return $this->_logger;
    }

    private function _parseError(ehough_shortstop_api_HttpResponse $response)
    {
        $entity = $response->getEntity();

        if ($entity === null) {

            throw new RuntimeException('Missing entity in response');
        }

        $rawResponse = $entity->getContent();

        preg_match('/.*<(?:title|internalreason)>([^<]+)<\/(?:title|internalreason)>.*/i', $rawResponse, $matches);

        if (count($matches) > 1) {

            return $matches[1];
        }

        return 'YouTube rejected the request due to malformed syntax.';
    }
}