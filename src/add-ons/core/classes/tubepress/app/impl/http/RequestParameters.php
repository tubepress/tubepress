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

/**
 * Pulls out info from $_GET or $_POST.
 */
class tubepress_app_impl_http_RequestParameters implements tubepress_lib_api_http_RequestParametersInterface
{
    /**
     * @var array A merged array of $_GET and $_POST for this request.
     */
    private $_cachedMergedGetAndPostArray;

    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * Gets the parameter value from PHP's $_GET or $_POST array.
     *
     * @param string $name The name of the parameter.
     *
     * @return mixed The raw value of the parameter. Can be anything that would
     *               otherwise be found in PHP's $_GET or $_POST array. Returns null
     *               if the parameter is not set on this request.
     *
     * @api
     * @since 4.0.0
     */
    public function getParamValue($name)
    {
        /** Are we sure we have it? */
        if (!($this->hasParam($name))) {

            return null;
        }

        $request  = $this->_getGETandPOSTarray();
        $rawValue = $request[$name];

        $event = $this->_eventDispatcher->newEventInstance(

            $rawValue,
            array('optionName' => $name)
        );

        $this->_eventDispatcher->dispatch(

            tubepress_app_api_event_Events::NVP_FROM_EXTERNAL_INPUT,
            $event
        );

        $event = $this->_eventDispatcher->newEventInstance($event->getSubject(), array(
            'optionName' => $name
        ));
        $this->_eventDispatcher->dispatch(

            tubepress_app_api_event_Events::NVP_FROM_EXTERNAL_INPUT . ".$name",
            $event
        );

        return $event->getSubject();
    }

    /**
     * Gets the parameter value from PHP's $_GET or $_POST array. If the hasParam($name) returs false, this
     *  behaves just like getParamvalue($name). Otherwise, if the raw parameter value is numeric, a conversion
     *  will be attempted.
     *
     * @param string $name    The name of the parameter.
     * @param int    $default The default value is the raw value is not integral.
     *
     * @return mixed The raw value of the parameter. Can be anything that would
     *               otherwise be found in PHP's $_GET or $_POST array. Returns null
     *               if the parameter is not set on this request.
     *
     * @api
     * @since 4.0.0
     */
    public function getParamValueAsInt($name, $default)
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
     *
     * @api
     * @since 4.0.0
     */
    public function hasParam($name)
    {
        $request = $this->_getGETandPOSTarray();

        return array_key_exists($name, $request);
    }

    /**
     * Returns a map of param name => param value for ALL parameters in the request.
     *
     * @return array A map of param name => param value for ALL parameters in the request.
     *
     * @api
     * @since 4.0.0
     */
    public function getAllParams()
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

