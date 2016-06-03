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

class tubepress_event_impl_tickertape_EventDispatcher implements tubepress_api_event_EventDispatcherInterface
{
    /**
     * @var \Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher
     */
    private $_wrappedDispatcher;

    public function __construct(\Symfony\Component\EventDispatcher\ContainerAwareEventDispatcher $dispatcher)
    {
        $this->_wrappedDispatcher = $dispatcher;
    }

    /**
     * {@inheritdoc}
     */
    public function addListener($eventName, $listener, $priority = 0)
    {
        $this->_wrappedDispatcher->addListener($eventName, $listener, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function addListenerService($eventName, $callback, $priority = 0)
    {
        $this->_wrappedDispatcher->addListenerService($eventName, $callback, $priority);
    }

    /**
     * {@inheritdoc}
     */
    public function dispatch($eventName, tubepress_api_event_EventInterface $event = null)
    {
        if (!$event || (!($event instanceof \Symfony\Component\EventDispatcher\Event))) {

            $event = new tubepress_event_impl_tickertape_TickertapeEventWrapper($event);
        }

        return $this->_wrappedDispatcher->dispatch($eventName, $event);
    }

    /**
     * {@inheritdoc}
     */
    public function getListeners($eventName = null)
    {
        return $this->_wrappedDispatcher->getListeners($eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function hasListeners($eventName = null)
    {
        return $this->_wrappedDispatcher->hasListeners($eventName);
    }

    /**
     * {@inheritdoc}
     */
    public function removeListener($eventName, $listener)
    {
        $this->_wrappedDispatcher->removeListener($eventName, $listener);
    }

    /**
     * {@inheritdoc}
     */
    public function newEventInstance($subject = null, array $arguments = array())
    {
        return new tubepress_event_impl_tickertape_EventBase($subject, $arguments);
    }
}
