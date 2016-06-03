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

class tubepress_wordpress_impl_listeners_wpfilter_PhotonListener
{
    /**
     * @var tubepress_wordpress_impl_wp_WpFunctions
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var tubepress_api_html_HtmlGeneratorInterface
     */
    private $_domainsToSearch;

    public function __construct(tubepress_api_url_UrlFactoryInterface   $urlFactory,
                                tubepress_api_util_StringUtilsInterface $stringUtils,
                                array $domainsToSearch)
    {
        $this->_urlFactory      = $urlFactory;
        $this->_stringUtils     = $stringUtils;
        $this->_domainsToSearch = $domainsToSearch;
    }

    public function onFilter_jetpack_photon_skip_for_url(tubepress_api_event_EventInterface $event)
    {
        $args     = $event->getArgument('args');
        $imageUrl = $args[0];

        try {

            $imageUrl = $this->_urlFactory->fromString($imageUrl);

        } catch (\InvalidArgumentException $e) {

            return;
        }

        $imageHost = $imageUrl->getHost();

        foreach ($this->_domainsToSearch as $domain) {

            if ($imageHost === "$domain" || $this->_stringUtils->endsWith($imageHost, ".$domain")) {

                $event->setSubject(true);

                return;
            }
        }
    }
}
