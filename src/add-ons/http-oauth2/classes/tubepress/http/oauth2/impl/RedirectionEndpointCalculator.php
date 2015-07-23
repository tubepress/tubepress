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
 *
 */
class tubepress_http_oauth2_impl_RedirectionEndpointCalculator
{
    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    public function __construct(tubepress_api_environment_EnvironmentInterface $environment,
                                tubepress_api_event_EventDispatcherInterface   $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
        $this->_environment     = $environment;
    }

    /**
     * @return tubepress_api_url_UrlInterface
     */
    public function getRedirectionEndpoint($providerName)
    {
        $baseUrl = $this->_environment->getBaseUrl()->getClone();
        $baseUrl->setPath('/web/php/oauth2');
        $baseUrl->getQuery()->set('provider', $providerName);

        $event = $this->_eventDispatcher->newEventInstance($baseUrl, array(
            'providerName' => $providerName
        ));

        $this->_eventDispatcher->dispatch(tubepress_api_event_Events::OAUTH2_URL_REDIRECTION_ENDPOINT, $event);

        $newUrl = $event->getSubject();

        if (!($newUrl instanceof tubepress_api_url_UrlInterface)) {

            throw new RuntimeException('Unable to calculate redirection endpoint.');
        }

        return $newUrl;
    }
}