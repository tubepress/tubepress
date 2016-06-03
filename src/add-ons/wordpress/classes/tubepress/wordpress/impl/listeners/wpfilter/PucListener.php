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

class tubepress_wordpress_impl_listeners_wpfilter_PucListener
{
    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_environment_EnvironmentInterface
     */
    private $_environment;

    /**
     * @var tubepress_api_options_ContextInterface
     */
    private $_context;

    public function __construct(tubepress_api_url_UrlFactoryInterface          $urlFactory,
                                tubepress_api_environment_EnvironmentInterface $environment,
                                tubepress_api_options_ContextInterface         $context)
    {
        $this->_urlFactory  = $urlFactory;
        $this->_environment = $environment;
        $this->_context     = $context;
    }

    public function onFilter_PucRequestInfoQueryArgsTubePress(tubepress_api_event_EventInterface $event)
    {
        $queryArgs = $event->getSubject();

        if ($this->_environment->isPro()) {

            $apiKey = $this->_context->get(tubepress_api_options_Names::TUBEPRESS_API_KEY);

            if ($apiKey) {

                $queryArgs['key'] = $apiKey;
                $queryArgs['pid'] = 2;
            }
        }

        $event->setSubject($queryArgs);
    }

    public function onFilter_PucRequestInfoResultTubePress(tubepress_api_event_EventInterface $event)
    {
        $pluginInfo = $event->getSubject();

        if ($pluginInfo && $this->_environment->isPro()) {

            $apiKey = $this->_context->get(tubepress_api_options_Names::TUBEPRESS_API_KEY);

            if (!$apiKey) {

                /*
                 * We don't want to downgrade Pro users that haven't entered an API key.
                 */
                $pluginInfo->download_url = null;
            }

            if (property_exists($pluginInfo, 'download_url') && strpos($pluginInfo->download_url, 'free') !== false) {

                /*
                 * Extra assurance that we don't downgrade Pro users
                 */
                $pluginInfo->download_url = null;
            }

            if (property_exists($pluginInfo, 'download_url') && $pluginInfo->download_url && $apiKey) {

                $url = $this->_urlFactory->fromString($pluginInfo->download_url);
                $url->getQuery()->set('key', $apiKey)->set('pid', 2);
                $pluginInfo->download_url = $url->toString();
            }

            $event->setSubject($pluginInfo);
        }
    }
}
