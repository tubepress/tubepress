<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * oauth v1 client.
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_lib_api_http_oauth_v1_ClientInterface
{
    /**
     * @ignore
     */
    const _ = 'tubepress_lib_api_http_oauth_v1_ClientInterface';

    /**
     * @param tubepress_lib_api_http_message_RequestInterface $request
     * @param tubepress_lib_api_http_oauth_v1_Credentials       $clientCredentials
     * @param tubepress_lib_api_http_oauth_v1_Credentials       $tokenCredentials
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function signRequest(tubepress_lib_api_http_message_RequestInterface $request,
                         tubepress_lib_api_http_oauth_v1_Credentials     $clientCredentials,
                         tubepress_lib_api_http_oauth_v1_Credentials     $tokenCredentials = null);
}