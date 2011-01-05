<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_video_feed_retrieval_AbstractFeedRetrievalService',
    'net_php_pear_Net_URL2',
    'net_php_pear_HTTP_Request2',
    'net_php_pear_HTTP_Request2_Adapter_Socket'));

/**
 *
 */
class org_tubepress_video_feed_retrieval_HTTPRequest2 extends org_tubepress_video_feed_retrieval_AbstractFeedRetrievalService
{
    /**
     * Retrieve the data from the network.
     *
     * @param string $urlAsString The URL to fetch from.
     *
     * @return unknown The network data.
     */
    protected function fetchFromNetwork($urlAsString)
    {
        $request = new net_php_pear_Net_URL2($urlAsString);
        $req     = new net_php_pear_HTTP_Request2($request);

        $req->setAdapter(new net_php_pear_HTTP_Request2_Adapter_Socket());

        $response = $req->send();

        org_tubepress_log_Log::log($this->getLogPrefix(), 'Request for <tt>%s</tt> returned status <tt>%d: %s</tt>', $request->getURL(true),
            $response->getStatus(), $response->getReasonPhrase());

        if ($response->getStatus() != 200) {
            throw new Exception(sprintf('Problem retrieving videos from provider: %s', $response->getReasonPhrase()));
        }

        return $response->getBody();
    }

    /**
     * Get the logging prefix.
     *
     * @return string The logging prefix.
     */
    protected function getLogPrefix()
    {
        return 'HTTP Request 2';
    }
}
