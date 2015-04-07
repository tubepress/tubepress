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
class tubepress_youtube3_impl_listeners_options_PlaylistIdListener
{
    /**
     * @var tubepress_platform_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_platform_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_platform_api_url_UrlFactoryInterface   $urlFactory,
                                tubepress_platform_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_urlFactory  = $urlFactory;
        $this->_stringUtils = $stringUtils;
    }

    public function onPreValidationOptionSet(tubepress_lib_api_event_EventInterface $event)
    {
        $filteredValue = $this->_maybeGetListValueFromUrl($event->getArgument('optionValue'));
        $filteredValue = $this->_maybeRemoveLeadingPL($filteredValue);

        $event->setArgument('optionValue', $filteredValue);
    }

    private function _maybeRemoveLeadingPL($originalValue)
    {
        if (!$this->_stringUtils->startsWith($originalValue, 'PL')) {

            return $originalValue;
        }

        return $this->_stringUtils->replaceFirst('PL', '', $originalValue);
    }

    private function _maybeGetListValueFromUrl($originalValue)
    {
        $url = null;

        try {

            $url = $this->_urlFactory->fromString($originalValue);

        } catch (Exception $e) {

            return $originalValue;
        }

        $host = $url->getHost();

        if (!$this->_stringUtils->endsWith($host, 'youtube.com')) {

            return $originalValue;
        }

        $params = $url->getQuery();

        if (!$params->hasKey('list') && !$params->hasKey('p')) {

            return $originalValue;
        }

        if ($params->hasKey('list')) {

            return $params->get('list');
        }

        return $params->get('p');
    }
}