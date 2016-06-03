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
class tubepress_dailymotion_impl_listeners_options_LanguageLocaleListener
{
    /**
     * @var array
     */
    private $_acceptableValues;

    /**
     * @var tubepress_dailymotion_impl_dmapi_LanguageSupplier|tubepress_dailymotion_impl_dmapi_LocaleSupplier
     */
    private $_supplier;

    public function __construct($supplier)
    {
        $this->_supplier = $supplier;
    }

    public function onAcceptableValues(tubepress_api_event_EventInterface $event)
    {
        if (!isset($this->_acceptableValues)) {

            $this->_acceptableValues = $this->_supplier->getValueMap();
        }

        $current = $event->getSubject();

        if (!is_array($current)) {

            $current = array();
        }

        $event->setSubject(array_merge($current, $this->_acceptableValues));
    }
}
