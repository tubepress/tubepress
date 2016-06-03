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
class tubepress_dailymotion_impl_listeners_options_transform_UserTransformer
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
        if ($this->_looksLikeDailymotionUser($incoming)) {

            //looks like a valid user idn
            return $incoming;
        }

        $incoming = trim($incoming, '/');

        /*
         * Might be
         *
         * 1. http://www.dailymotion.com/user/foobar/1
         * 2. http://www.dailymotion.com/user/foobar
         * 3. http://www.dailymotion.com/foobar
         */

        try {

            $url  = $this->_urlFactory->fromString($incoming);
            $host = $url->getHost();

            if (!$this->_stringUtils->endsWith($host, 'dailymotion.com')) {

                return null;
            }

            $pathSegments = $url->getPathSegments();
            $pathCount    = count($pathSegments);

            if ($pathCount < 2) {

                return null;
            }

            if ($pathCount > 2 && $pathSegments[1] !== 'user') {

                return null;
            }

            if ($pathCount > 2) {

                $user = $pathSegments[2];

            } else {

                $user = $pathSegments[1];
            }

            $error = $user === 'user' || !$this->_looksLikeDailymotionUser($user);

            if ($error) {

                return null;
            }

            return $user;

        } catch (InvalidArgumentException $e) {

            //invalid URL
            return null;
        }
    }

    private function _looksLikeDailymotionUser($candidate)
    {
        return $candidate && preg_match_all('~^[a-zA-Z0-9-_]+$~', $candidate, $matches) === 1;
    }
}
