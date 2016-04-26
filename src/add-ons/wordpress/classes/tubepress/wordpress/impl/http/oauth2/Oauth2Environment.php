<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_wordpress_impl_http_oauth2_Oauth2Environment implements tubepress_api_http_oauth2_Oauth2EnvironmentInterface
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_wpFunctions;

    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    /**
     * @var string
     */
    private $_csrf;

    public function __construct(tubepress_api_url_UrlFactoryInterface        $urlFactory,
                                tubepress_wordpress_impl_wp_WpFunctions      $wpFunctions,
                                tubepress_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_wpFunctions     = $wpFunctions;
        $this->_urlFactory      = $urlFactory;
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getRedirectionUrl(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $url = $this->_startAdminUrl('tubepress_oauth2', $provider);

        return $this->_dispatchUrl(

            tubepress_api_event_Events::OAUTH2_URL_REDIRECTION_ENDPOINT,
            $url,
            $provider
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthorizationInitiationUrl(tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        return $this->_startAdminUrl('tubepress_oauth2_start', $provider);
    }

    /**
     * {@inheritdoc}
     */
    public function getCsrfSecret()
    {
        if (!isset($this->_csrf)) {

            if (defined('AUTH_KEY')) {

                $seed = AUTH_KEY;

            } else {

                /* @noinspection PhpUndefinedConstantInspection */
                $seed = DB_NAME . DB_PASSWORD . ABSPATH;
            }

            $this->_csrf = md5($seed);
        }

        return $this->_csrf;
    }

    /**
     * @return tubepress_api_url_UrlInterface
     */
    private function _startAdminUrl($pageSlug, tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $asString     = $this->_wpFunctions->admin_url('admin.php');
        $toReturn     = $this->_urlFactory->fromString($asString);
        $providerName = $provider->getName();

        $toReturn->getQuery()->set('page', $pageSlug)
                             ->set('provider', $providerName)
                             ->set('csrf_token', $this->getCsrfSecret());

        return $toReturn;
    }

    /**
     * @param                                                   $eventName
     * @param tubepress_api_url_UrlInterface                    $url
     * @param tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider
     *
     * @return tubepress_api_url_UrlInterface
     */
    private function _dispatchUrl($eventName, tubepress_api_url_UrlInterface $url, tubepress_spi_http_oauth2_Oauth2ProviderInterface $provider)
    {
        $event = $this->_eventDispatcher->newEventInstance($url, array(
            'provider' => $provider,
        ));

        $this->_eventDispatcher->dispatch($eventName, $event);

        $newUrl = $event->getSubject();

        if (!($newUrl instanceof tubepress_api_url_UrlInterface)) {

            throw new RuntimeException('Unable to calculate redirection endpoint.');
        }

        return $newUrl;
    }
}
