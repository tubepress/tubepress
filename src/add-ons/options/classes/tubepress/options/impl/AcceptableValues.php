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

class tubepress_options_impl_AcceptableValues implements tubepress_api_options_AcceptableValuesInterface
{
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
    public function getAcceptableValues($optionName)
    {
        $event = $this->_eventDispatcher->newEventInstance(null, array(
            'optionName' => $optionName,
        ));
        $this->_eventDispatcher->dispatch(

            tubepress_api_event_Events::OPTION_ACCEPTABLE_VALUES . ".$optionName",
            $event
        );

        return $event->getSubject();
    }
}
