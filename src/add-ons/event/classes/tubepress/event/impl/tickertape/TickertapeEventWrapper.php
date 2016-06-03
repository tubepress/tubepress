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

/**
 * Providers a wrapper to allow tubepress_api_event_EventInterface instances to be passed off as
 * ehough_tickertape_Event instances.
 */
class tubepress_event_impl_tickertape_TickertapeEventWrapper extends \Symfony\Component\EventDispatcher\Event implements tubepress_api_event_EventInterface
{
    /**
     * @var tubepress_api_event_EventInterface
     */
    private $_delegate;

    /** @noinspection PhpMissingParentConstructorInspection */
    public function __construct(tubepress_api_event_EventInterface $event = null)
    {
        if (!$event) {

            $event = new tubepress_event_impl_tickertape_EventBase();
        }

        $this->_delegate = $event;
    }

    /**
     * {@inheritdoc}
     */
    public function getArgument($key)
    {
        return $this->_delegate->getArgument($key);
    }

    /**
     * {@inheritdoc}
     */
    public function getArguments()
    {
        return $this->_delegate->getArguments();
    }

    /**
     * {@inheritdoc}
     */
    public function getDispatcher()
    {
        return $this->_delegate->getDispatcher();
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->_delegate->getName();
    }

    /**
     * {@inheritdoc}
     */
    public function getSubject()
    {
        return $this->_delegate->getSubject();
    }

    /**
     * {@inheritdoc}
     */
    public function hasArgument($key)
    {
        return $this->_delegate->hasArgument($key);
    }

    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped()
    {
        return $this->_delegate->isPropagationStopped();
    }

    /**
     * {@inheritdoc}
     */
    public function setArgument($key, $value)
    {
        return $this->_delegate->setArgument($key, $value);
    }

    /**
     * {@inheritdoc}
     */
    public function setArguments(array $args = array())
    {
        return $this->_delegate->setArguments($args);
    }

    /**
     * {@inheritdoc}
     */
    public function setSubject($subject)
    {
        $this->_delegate->setSubject($subject);
    }

    /**
     * {@inheritdoc}
     */
    public function stopPropagation()
    {
        $this->_delegate->stopPropagation();
    }

    /**
     * {@inheritdoc}
     */
    public function setName($name)
    {
        $this->_delegate->setName($name);
    }
}
