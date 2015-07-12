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
class tubepress_vimeo2_impl_listeners_options_VimeoOptionsListener
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

    public function onAlbumValue(tubepress_lib_api_event_EventInterface $event)
    {
        $filteredValue = $this->_maybeConvertUrl($event->getArgument('optionValue'), 'album');

        $event->setArgument('optionValue', $filteredValue);
    }

    public function onChannelValue(tubepress_lib_api_event_EventInterface $event)
    {
        $filteredValue = $this->_maybeConvertUrl($event->getArgument('optionValue'), 'channels');

        $event->setArgument('optionValue', $filteredValue);
    }

    public function onGroupValue(tubepress_lib_api_event_EventInterface $event)
    {
        $filteredValue = $this->_maybeConvertUrl($event->getArgument('optionValue'), 'groups');

        $event->setArgument('optionValue', $filteredValue);
    }

    private function _maybeConvertUrl($originalValue, $pathSegment)
    {
        $url = null;

        try {

            $url = $this->_urlFactory->fromString($originalValue);

        } catch (Exception $e) {

            return $originalValue;
        }

        $host = $url->getHost();

        if (!$this->_stringUtils->endsWith($host, 'vimeo.com')) {

            return $originalValue;
        }

        $path                  = $url->getPath();
        $pathStartsWithSegment = $this->_stringUtils->startsWith($path, '/' . $pathSegment);

        if (!$pathStartsWithSegment) {

            return $originalValue;
        }

        $explodedPath = preg_split('~/~', $path);

        if (count($explodedPath) < 3) {

            return $originalValue;
        }

        return $explodedPath[2];
    }
}