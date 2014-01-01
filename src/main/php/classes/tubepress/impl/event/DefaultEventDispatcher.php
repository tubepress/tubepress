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

class tubepress_impl_event_DefaultEventDispatcher implements tubepress_api_event_EventDispatcherInterface
{
    /**
     * @var ehough_tickertape_ContainerAwareEventDispatcher
     */
    private $_wrappedDispatcher;

    public function __construct(ehough_tickertape_ContainerAwareEventDispatcher $dispatcher)
    {
        $this->_wrappedDispatcher = $dispatcher;
    }

    /**
     * Adds an event listener that listens on the specified events.
     *
     * @param string   $eventName The event to listen on
     * @param callable $listener  The listener
     * @param integer  $priority  The higher this value, the earlier an event
     *                            listener will be triggered in the chain (defaults to 0)
     *
     * @api
     * @since 3.1.0
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->_wrappedDispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * Adds a service as event listener
     *
     * @param string  $eventName   Event for which the listener is added
     * @param array   $callback    The service ID of the listener service & the method
     *                             name that has to be called
     * @param integer $priority    The higher this value, the earlier an event listener
     *                             will be triggered in the chain.
     *                             Defaults to 0.
     *
     * @throws InvalidArgumentException
     *
     * @api
     * @since 3.1.0
     */
    public function addListenerService($eventName, $callback, $priority = 0)
    {
        $this->_wrappedDispatcher->addListenerService($eventName, $callback, $priority);
    }

    /**
     * Dispatches an event to all registered listeners.
     *
     * @param string                             $eventName The name of the event to dispatch. The name of
     *                                                      the event is the name of the method that is
     *                                                      invoked on listeners.
     * @param tubepress_api_event_EventInterface $event     The event to pass to the event handlers/listeners.
     *                                                      If not supplied, an empty event instance is created.
     *
     * @return tubepress_api_event_EventInterface
     *
     * @api
     * @since 3.1.0
     */
    public function dispatch($eventName, tubepress_api_event_EventInterface $event = null)
    {
        if (!$event || (!($event instanceof ehough_tickertape_Event))) {

            $event = new tubepress_impl_event_TickertapeEventWrapper($event);
        }

        return $this->_wrappedDispatcher->dispatch($eventName, $event);
    }

    /**
     * Gets the listeners of a specific event or all listeners.
     *
     * @param string $eventName The name of the event
     *
     * @return array The event listeners for the specified event, or all event listeners by event name
     *
     * @api
     * @since 3.1.0
     */
    public function getListeners($eventName = null)
    {
        return $this->_wrappedDispatcher->getListeners($eventName);
    }

    /**
     * Checks whether an event has any registered listeners.
     *
     * @param string $eventName The name of the event
     *
     * @return Boolean true if the specified event has any listeners, false otherwise
     *
     * @api
     * @since 3.1.0
     */
    public function hasListeners($eventName = null)
    {
        return $this->_wrappedDispatcher->hasListeners($eventName);
    }

    /**
     * Removes an event listener from the specified events.
     *
     * @param string|array $eventName The event(s) to remove a listener from
     * @param callable     $listener  The listener to remove
     *
     * @api
     * @since 3.1.0
     */
    public function removeListener($eventName, $listener)
    {
        $this->_wrappedDispatcher->removeListener($eventName, $listener);
    }
}