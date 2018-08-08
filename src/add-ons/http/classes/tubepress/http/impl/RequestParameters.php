<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
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
class tubepress_http_impl_RequestParameters implements tubepress_api_http_RequestParametersInterface
{
    /**
     * @var array A merged array of and $_POST for this request.
     */
    private $_cachedMergedGetAndPostArray;

    /**
     * @var tubepress_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function getParamValue($name)
    {
        /* Are we sure we have it? */
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

            tubepress_api_event_Events::NVP_FROM_EXTERNAL_INPUT,
            $event
        );

        $event = $this->_eventDispatcher->newEventInstance($event->getSubject(), array(
            'optionName' => $name,
        ));
        $this->_eventDispatcher->dispatch(

            tubepress_api_event_Events::NVP_FROM_EXTERNAL_INPUT . ".$name",
            $event
        );

        return $event->getSubject();
    }

    /**
     * {@inheritdoc}
     */
    public function getParamValueAsInt($name, $default)
    {
        $raw = $this->getParamValue($name);

        /* Not numeric? */
        if (!is_numeric($raw) || ($raw < 1)) {

            return $default;
        }

        return (int) $raw;
    }

    /**
     * {@inheritdoc}
     */
    public function hasParam($name)
    {
        $request = $this->_getGETandPOSTarray();

        return array_key_exists($name, $request);
    }

    /**
     * {@inheritdoc}
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
        if (!isset($this->_cachedMergedGetAndPostArray)) {

            $this->_cachedMergedGetAndPostArray = array_merge($_GET, $_POST);
        }

        return $this->_cachedMergedGetAndPostArray;
    }
}
