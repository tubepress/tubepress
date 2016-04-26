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
class tubepress_dailymotion_impl_listeners_options_transform_PlaylistTransformer
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

    public function transform($incoming)
    {
        if ($this->_looksLikeDailymotionPlaylistId($incoming)) {

            //looks like a valid video ID
            return $incoming;
        }

        // x2xv8yy_description
        if (preg_match_all('~^[a-z0-9]+_.+$~', $incoming, $matches) === 1) {

            $exploded = explode('_', $incoming);

            return $exploded[0];
        }

        $incoming = trim($incoming, '/');

        /*
         * Might be
         *
         * 1. http://www.dailymotion.com/playlist/playlistid
         * 2. http://www.dailymotion.com/playlist/playlistid_description
         */

        try {

            $url  = $this->_urlFactory->fromString($incoming);
            $host = $url->getHost();
            $path = $url->getPath();

            if (!$this->_stringUtils->endsWith($host, 'dailymotion.com')) {

                return null;
            }

            if (!$this->_stringUtils->startsWith($path, '/playlist/')) {

                return null;
            }

            $one        = 1;
            $path       = str_replace('/playlist/', '', $path, $one);
            $exploded   = explode('_', $path);
            $playlistId = $exploded[0];

            $error = !$this->_looksLikeDailymotionPlaylistId($playlistId);

            if ($error) {

                return null;
            }

            return $playlistId;

        } catch (InvalidArgumentException $e) {

            //invalid URL
            return null;
        }
    }

    private function _looksLikeDailymotionPlaylistId($candidate)
    {
        return $candidate && preg_match_all('~^[a-z0-9]+$~', $candidate, $matches) === 1;
    }
}
