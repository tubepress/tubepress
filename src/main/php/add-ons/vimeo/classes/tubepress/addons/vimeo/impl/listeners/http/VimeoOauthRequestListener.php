<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
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
class tubepress_addons_vimeo_impl_listeners_http_VimeoOauthRequestListener
{
    /**
     * @var ehough_coauthor_api_v1_ClientInterface
     */
    private $_oauthClient;

    /**
     * @var tubepress_spi_context_ExecutionContext
     */
    private $_executionContext;

    public function __construct(ehough_coauthor_api_v1_ClientInterface $oauthClient, tubepress_spi_context_ExecutionContext $context)
    {
        $this->_executionContext = $context;
        $this->_oauthClient      = $oauthClient;
    }

    public final function onRequest(ehough_tickertape_GenericEvent $event)
    {
        /**
         * @var $request ehough_shortstop_api_HttpRequest
         */
        $request  = $event->getSubject();

        if ($request->getUrl()->getHost() !== 'vimeo.com') {

            //not a Vimeo request
            return;
        }

        $clientId          = $this->_executionContext->get(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_KEY);
        $clientSecret      = $this->_executionContext->get(tubepress_addons_vimeo_api_const_options_names_Feed::VIMEO_SECRET);
        $clientCredentials = new ehough_coauthor_api_v1_Credentials($clientId, $clientSecret);

        $this->_oauthClient->signRequest($request, $clientCredentials);
    }
}