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
 *
 */
class tubepress_app_impl_options_AcceptableValues implements tubepress_app_api_options_AcceptableValuesInterface
{
    /**
     * @var tubepress_lib_api_event_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_lib_api_event_EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    /**
     * @param $optionName string The option name.
     *
     * @return array An associative array of values to untranslated descriptions that the given
     *               option can accept. May be null if the option does not support discrete values.
     *
     * @api
     * @since 4.0.0
     */
    public function getAcceptableValues($optionName)
    {
        $event = $this->_eventDispatcher->newEventInstance(null, array(
            'optionName' => $optionName
        ));
        $this->_eventDispatcher->dispatch(

            tubepress_app_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
            $event
        );

        return $event->getSubject();
    }
}