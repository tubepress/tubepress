<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * Providers a wrapper to allow tubepress_api_event_EventInterface instances to be passed off as
 * ehough_tickertape_Event instances.
 */
class tubepress_impl_event_TickertapeEventWrapper extends ehough_tickertape_Event implements tubepress_api_event_EventInterface
{
    /**
     * @var tubepress_api_event_EventInterface
     */
    private $_delegate;

    public function __construct(tubepress_api_event_EventInterface $event)
    {
        $this->_delegate = $event;
    }

    /**
     * Get argument by key.
     *
     * @param string $key Key.
     *
     * @throws InvalidArgumentException If key is not found.
     *
     * @return mixed Contents of array key.
     */
    public function getArgument($key)
    {
        return $this->_delegate->getArgument($key);
    }

    /**
     * Getter for all arguments.
     *
     * @return array
     */
    public function getArguments()
    {
        return $this->_delegate->getArguments();
    }

    /**
     * @return mixed The subject of the event.
     */
    public function getSubject()
    {
        return $this->_delegate->getSubject();
    }

    /**
     * Has argument.
     *
     * @param string $key Key of arguments array.
     *
     * @return boolean
     */
    public function hasArgument($key)
    {
        return $this->_delegate->hasArgument($key);
    }

    /**
     * Add argument to event.
     *
     * @param string $key   Argument name.
     * @param mixed  $value Value.
     *
     * @return ehough_tickertape_GenericEvent
     */
    public function setArgument($key, $value)
    {
        return $this->_delegate->setArgument($key, $value);
    }

    /**
     * Set args property.
     *
     * @param array $args Arguments.
     *
     * @return ehough_tickertape_GenericEvent
     */
    public function setArguments(array $args = array())
    {
        return $this->_delegate->setArguments($args);
    }

    /**
     * Allows listeners to replace the subject with a new item.
     *
     * @param mixed $subject The new subject for the event.
     *
     * @return void
     */
    public function setSubject($subject)
    {
        $this->_delegate->setSubject($subject);
    }
}