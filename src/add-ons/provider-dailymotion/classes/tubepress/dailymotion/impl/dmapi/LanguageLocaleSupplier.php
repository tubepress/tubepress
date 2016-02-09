<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_dailymotion_impl_dmapi_LanguageLocaleSupplier
{
    /**
     * @var tubepress_api_url_UrlFactoryInterface
     */
    private $_urlFactory;

    /**
     * @var tubepress_api_util_StringUtilsInterface
     */
    private $_stringUtils;

    /**
     * @var tubepress_dailymotion_impl_dmapi_ApiUtility
     */
    private $_apiUtility;

    /**
     * @var tubepress_api_array_ArrayReaderInterface
     */
    private $_arrayReader;

    /**
     * @var array
     */
    private $_cache;

    /**
     * @var string
     */
    private $_urlAsString;

    /**
     * @var string
     */
    private $_codeKey;

    /**
     * @var string[]
     */
    private $_displayNameKeys;

    public function __construct(tubepress_api_url_UrlFactoryInterface       $urlFactory,
                                tubepress_api_util_StringUtilsInterface     $stringUtils,
                                tubepress_dailymotion_impl_dmapi_ApiUtility $apiUtility,
                                $urlAsString,
                                $codeKey,
                                array $displayNameKeys)
    {
        foreach ($displayNameKeys as $key) {

            if (!is_string($key)) {

                throw new InvalidArgumentException('Display name keys must be strings only');
            }
        }

        $this->_urlFactory      = $urlFactory;
        $this->_stringUtils     = $stringUtils;
        $this->_apiUtility      = $apiUtility;
        $this->_arrayReader     = new tubepress_array_impl_ArrayReader();
        $this->_urlAsString     = (string) $urlAsString;
        $this->_codeKey         = (string) $codeKey;
        $this->_displayNameKeys = $displayNameKeys;
    }

    public function getValueMap()
    {
        if (!isset($this->_cache)) {

            $url          = $this->_urlFactory->fromString($this->_urlAsString);
            $response     = $this->_apiUtility->getDecodedApiResponse($url);
            $list         = $this->_arrayReader->getAsArray($response, 'list');
            $this->_cache = array();

            foreach ($list as $entry) {

                if (!isset($entry[$this->_codeKey])) {

                    continue;
                }

                $code = $entry[$this->_codeKey];

                foreach ($this->_displayNameKeys as $displayNameKey) {

                    if (isset($entry[$displayNameKey])) {

                        $this->_cache[$code] = $entry[$displayNameKey];
                        break;
                    }
                }
            }
        }

        return $this->_cache;
    }
}