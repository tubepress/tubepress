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
 * An event that is passed around during TubePress's execution.
 *
 */
interface tubepress_api_event_EventInterface
{
    /**
     * Get argument by key.
     *
     * @param string $key Key.
     *
     * @throws InvalidArgumentException If key is not found.
     *
     * @return mixed Contents of array key.
     *
     * @api
     * @since 3.1.0
     */
    function getArgument($key);

    /**
     * Getter for all arguments.
     *
     * @return array
     *
     * @api
     * @since 3.1.0
     */
    function getArguments();

    /**
     * Returns the EventDispatcher that dispatches this Event
     *
     * @return tubepress_api_event_EventDispatcherInterface
     *
     * @api
     * @since 3.1.0
     */
    function getDispatcher();

    /**
     * Gets the event's name.
     *
     * @return string
     *
     * @api
     * @since 3.1.0
     */
    function getName();

    /**
     * @return mixed The subject of the event.
     *
     * @api
     * @since 3.1.0
     */
    function getSubject();

    /**
     * Has argument.
     *
     * @param string $key Key of arguments array.
     *
     * @return boolean
     *
     * @api
     * @since 3.1.0
     */
    function hasArgument($key);

    /**
     * Returns whether further event listeners should be triggered.
     *
     * @return Boolean Whether propagation was already stopped for this event.
     *
     * @api
     * @since 3.1.0
     */
    function isPropagationStopped();

    /**
     * Add argument to event.
     *
     * @param string $key   Argument name.
     * @param mixed  $value Value.
     *
     * @return tubepress_api_event_EventInterface
     *
     * @api
     * @since 3.1.0
     */
    function setArgument($key, $value);

    /**
     * Set args property.
     *
     * @param array $args Arguments.
     *
     * @return tubepress_api_event_EventInterface
     *
     * @api
     * @since 3.1.0
     */
    function setArguments(array $args = array());

    /**
     * Allows listeners to replace the subject with a new item.
     *
     * @param mixed $subject The new subject for the event.
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    function setSubject($subject);

    /**
     * Stops the propagation of the event to further event listeners.
     *
     * If multiple event listeners are connected to the same event, no
     * further event listener will be triggered once any trigger calls
     * stopPropagation().
     *
     * @return void
     *
     * @api
     * @since 3.1.0
     */
    function stopPropagation();
}
