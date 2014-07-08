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
 *
 */
class tubepress_app_options_impl_internal_AbstractOptionReader
{
    /**
     * @var tubepress_lib_event_api_EventDispatcherInterface
     */
    private $_eventDispatcher;

    public function __construct(tubepress_lib_event_api_EventDispatcherInterface $eventDispatcher)
    {
        $this->_eventDispatcher = $eventDispatcher;
    }

    protected function getErrors($optionName, &$optionValue)
    {
        $event = $this->_dispatch($optionName, $optionValue, array(), tubepress_app_options_api_Constants::EVENT_NVP_READ_FROM_EXTERNAL_INPUT);
        $event = $this->_dispatch(
            $optionName,
            $event->getArgument('optionValue'),
            $event->getSubject(),
            tubepress_app_options_api_Constants::EVENT_OPTION_SET . '.' . $optionName
        );
        $event = $this->_dispatch($optionName,
            $event->getArgument('optionValue'),
            $event->getSubject(),
            tubepress_app_options_api_Constants::EVENT_OPTION_SET
        );

        $optionValue = $event->getArgument('optionValue');

        return $event->getSubject();
    }

    /**
     * @param $optionName
     * @param $optionValue
     * @param array $errors
     * @param $eventName
     * @return tubepress_lib_event_api_EventInterface
     */
    private function _dispatch($optionName, $optionValue, array $errors, $eventName)
    {
        $event = $this->_eventDispatcher->newEventInstance($errors, array(

            'optionName'  => $optionName,
            'optionValue' => $optionValue
        ));

        $this->_eventDispatcher->dispatch($eventName, $event);

        return $event;
    }
}
