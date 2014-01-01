<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Pulls out info from $_GET or $_POST.
 */
class tubepress_impl_http_DefaultHttpRequestParameterService implements tubepress_spi_http_HttpRequestParameterService
{
    /**
     * @var array A merged array of $_GET and $_POST for this request.
     */
    private $_cachedMergedGetAndPostArray;

    /**
     * Gets the parameter value from PHP's $_GET or $_POST array.
     *
     * @param string $name The name of the parameter.
     *
     * @return mixed The raw value of the parameter. Can be anything that would
     *               otherwise be found in PHP's $_GET or $_POST array. Returns null
     *               if the parameter is not set on this request.
     */
    public final function getParamValue($name)
    {
        /** Are we sure we have it? */
        if (!($this->hasParam($name))) {

            return null;
        }

        $request  = $this->_getGETandPOSTarray();
        $rawValue = $request[$name];

        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $event = new tubepress_spi_event_EventBase(

            $rawValue,
            array('optionName' => $name)
        );

        $eventDispatcher->dispatch(

            tubepress_api_const_event_EventNames::OPTIONS_NVP_READFROMEXTERNAL,
            $event
        );

        return $event->getSubject();
    }

    /**
     * Gets the parameter value from PHP's $_GET or $_POST array. If the hasParam($name) returns false, this
     *  behaves just like getParamvalue($name). Otherwise, if the raw parameter value is numeric, a conversion
     *  will be attempted.
     *
     * @param string $name    The name of the parameter.
     * @param int    $default The default value is the raw value is not integral.
     *
     * @return mixed The raw value of the parameter. Can be anything that would
     *                       otherwise be found in PHP's $_GET or $_POST array. Returns null
     *                       if the parameter is not set on this request.
     */
    public final function getParamValueAsInt($name, $default)
    {
        $raw = $this->getParamValue($name);

        /** Not numeric? */
        if (! is_numeric($raw) || ($raw < 1)) {

            return $default;
        }

        return (int) $raw;
    }

    /**
     * Determines if the parameter is set in PHP's $_GET or $_POST array.
     *
     * @param string $name The name of the parameter.
     *
     * @return mixed True if the parameter is found in PHP's $_GET or $_POST array, false otherwise.
     */
    public final function hasParam($name)
    {
        $request = $this->_getGETandPOSTarray();

        return array_key_exists($name, $request);
    }

    /**
     * Returns a map of param name => param value for ALL parameters in the request.
     *
     * @return array A map of param name => param value for ALL parameters in the request.
     */
    public final function getAllParams()
    {
        $toReturn = array();
        $request  = $this->_getGETandPOSTarray();

        foreach ($request as $key => $value) {

            $toReturn[$key] = $this->getParamValue($key);
        }

        return $toReturn;
    }

    private function _getGETandPOSTarray()
    {
        if (! isset($this->_cachedMergedGetAndPostArray)) {

            $this->_cachedMergedGetAndPostArray = array_merge($_GET, $_POST);
        }

        return $this->_cachedMergedGetAndPostArray;
    }
}

