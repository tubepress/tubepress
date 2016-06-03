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
class tubepress_youtube3_impl_listeners_options_YouTubeOptionListener
{
    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    public function __construct(tubepress_api_url_UrlFactoryInterface   $urlFactory,
                                tubepress_api_util_StringUtilsInterface $stringUtils)
    {
        $this->_urlFactory  = $urlFactory;
        $this->_stringUtils = $stringUtils;
    }

    public function onPlaylistValue(tubepress_api_event_EventInterface $event)
    {
        $filteredValue = $this->_maybeGetPlaylistValueFromUrl($event->getArgument('optionValue'));
        $filteredValue = $this->_maybeRemoveLeadingPL($filteredValue);

        $event->setArgument('optionValue', $filteredValue);
    }

    public function onUserOrFavoritesValue(tubepress_api_event_EventInterface $event)
    {
        $filteredValue = $this->_maybeConvertUserOrChannel($event->getArgument('optionValue'));

        $event->setArgument('optionValue', $filteredValue);
    }

    public function onRelatedToValue(tubepress_api_event_EventInterface $event)
    {
        $filteredValue = $this->_maybeGetVideoId($event->getArgument('optionValue'));

        $event->setArgument('optionValue', $filteredValue);
    }

    public function onListValue(tubepress_api_event_EventInterface $event)
    {
        $filteredValue = $this->_normalizeListValue($event->getArgument('optionValue'));

        $event->setArgument('optionValue', $filteredValue);
    }

    private function _normalizeListValue($candidate)
    {
        if (!is_string($candidate)) {

            return $candidate;
        }

        $exploded = preg_split('~\s*,\s*~', $candidate);

        if (count($exploded) === 0) {

            return $candidate;
        }

        $collection = array();

        foreach ($exploded as $candidateId) {

            $collection[] = $this->_maybeGetVideoId($candidateId);
        }

        return implode(',', $collection);
    }

    private function _maybeConvertUserOrChannel($originalValue)
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

        $path                  = $url->getPath();
        $pathStartsWithChannel = $this->_stringUtils->startsWith($path, '/channel');
        $pathStartsWithUser    = $this->_stringUtils->startsWith($path, '/user');

        if (!$pathStartsWithChannel && !$pathStartsWithUser) {

            return $originalValue;
        }

        $explodedPath = preg_split('~/~', $path);

        if (count($explodedPath) < 3) {

            return $originalValue;
        }

        return $explodedPath[2];
    }

    private function _maybeRemoveLeadingPL($originalValue)
    {
        if (!$this->_stringUtils->startsWith($originalValue, 'PL')) {

            return $originalValue;
        }

        return $this->_stringUtils->replaceFirst('PL', '', $originalValue);
    }

    private function _maybeGetPlaylistValueFromUrl($originalValue)
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

    /**
     * https://stackoverflow.com/questions/7693218/youtube-i-d-parsing-for-new-url-formats
     */
    private function _maybeGetVideoId($candidate)
    {
        if (!is_string($candidate)) {

            return $candidate;
        }

        $pattern = '#^(?:https?://)?(?:www\.)?(?:youtu\.be/|youtube\.com(?:/embed/|/v/|/watch\?v=|/watch\?.+&v=))([\w-]{11})(?:.+)?$#x';

        preg_match($pattern, trim($candidate), $matches);

        return (isset($matches[1])) ? $matches[1] : $candidate;
    }
}
