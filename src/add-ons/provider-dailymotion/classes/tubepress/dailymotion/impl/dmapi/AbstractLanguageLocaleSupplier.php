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
abstract class tubepress_dailymotion_impl_dmapi_AbstractLanguageLocaleSupplier
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

    public function __construct(tubepress_api_url_UrlFactoryInterface       $urlFactory,
                                tubepress_api_util_StringUtilsInterface     $stringUtils,
                                tubepress_dailymotion_impl_dmapi_ApiUtility $apiUtility,
                                $urlAsString,
                                $codeKey)
    {
        $this->_urlFactory  = $urlFactory;
        $this->_stringUtils = $stringUtils;
        $this->_apiUtility  = $apiUtility;
        $this->_arrayReader = new tubepress_array_impl_ArrayReader();
        $this->_urlAsString = (string) $urlAsString;
        $this->_codeKey     = (string) $codeKey;
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

                $code        = $entry[$this->_codeKey];
                $displayName = $this->getDisplayNameFromCode($code, $entry);

                if ($displayName) {

                    $this->_cache[$code] = "$code - $displayName";
                }
            }

            ksort($this->_cache);

            $this->_cache = array_merge(

                array('none' => 'select ...'),
                $this->_cache
            );
        }

        return $this->_cache;
    }

    /**
     * @param       $code
     * @param array $entry
     *
     * @return string|null
     */
    protected abstract function getDisplayNameFromCode($code, array $entry);
}
