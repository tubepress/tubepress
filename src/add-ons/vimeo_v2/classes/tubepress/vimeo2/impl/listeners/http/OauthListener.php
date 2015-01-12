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
 * Signs OAuth requests for Vimeo.
 */
class tubepress_vimeo2_impl_listeners_http_OauthListener
{
    /**
     * @var tubepress_lib_api_http_oauth_v1_ClientInterface
     */
    private $_oauthClient;

    /**
     * @var tubepress_app_api_options_ContextInterface
     */
    private $_executionContext;

    public function __construct(tubepress_lib_api_http_oauth_v1_ClientInterface $oauthClient,
                                tubepress_app_api_options_ContextInterface      $context)
    {
        $this->_executionContext = $context;
        $this->_oauthClient      = $oauthClient;
    }

    public function onRequest(tubepress_lib_api_event_EventInterface $event)
    {
        /**
         * @var $request tubepress_lib_api_http_message_RequestInterface
         */
        $request  = $event->getSubject();

        if ($request->getUrl()->getHost() !== 'vimeo.com') {

            //not a Vimeo request
            return;
        }

        $clientId          = $this->_executionContext->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_KEY);
        $clientSecret      = $this->_executionContext->get(tubepress_vimeo2_api_Constants::OPTION_VIMEO_SECRET);
        $clientCredentials = new tubepress_lib_api_http_oauth_v1_Credentials($clientId, $clientSecret);

        $this->_oauthClient->signRequest($request, $clientCredentials);
    }
}