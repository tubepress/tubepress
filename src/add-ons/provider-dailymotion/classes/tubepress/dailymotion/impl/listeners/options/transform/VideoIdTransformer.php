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
class tubepress_dailymotion_impl_listeners_options_transform_VideoIdTransformer
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
        if ($this->_looksLikeDailymotionVideoId($incoming)) {

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
         * 1. http://www.dailymotion.com/video/videoid
         * 2. http://www.dailymotion.com/video/videoid_description
         */

        try {

            $url  = $this->_urlFactory->fromString($incoming);
            $host = $url->getHost();
            $path = $url->getPath();

            if (!$this->_stringUtils->endsWith($host, 'dailymotion.com')) {

                return null;
            }

            if (!$this->_stringUtils->startsWith($path, '/video/') && !$this->_stringUtils->startsWith($path, '/hub/')) {

                return null;
            }

            $one      = 1;
            $path     = str_replace(array('/video/', '/hub/'), '', $path, $one);
            $exploded = explode('_', $path);
            $videoId  = $exploded[0];

            $error = !$this->_looksLikeDailymotionVideoId($videoId);

            if ($error) {

                return null;
            }

            return $videoId;

        } catch (InvalidArgumentException $e) {

            //invalid URL
            return null;
        }
    }

    private function _looksLikeDailymotionVideoId($candidate)
    {
        return $candidate && preg_match_all('~^[a-z0-9]+$~', $candidate, $matches) === 1;
    }
}
