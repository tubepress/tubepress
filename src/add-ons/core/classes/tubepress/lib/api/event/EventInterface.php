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
 * An event that is passed around during TubePress's execution.
 *
 * Oftentimes, different add-ons need to be notified of
 * certain events taking place during the execution of TubePress (e.g., notify prior to JS scripts being written to HTML
 * so additional JS can be added, or inform when the video gallery is about to be displayed so custom code can be
 * executed). The `EventInterface` allows for the event itself to be modified through the `getSubject` and `setSubject`
 * functions described below, as well as for the passing of related objects through the `getArgument` (`getArguments`)
 * and `setArgument` (`setArguments`) functions described below.
 *
 * @package TubePress\Event
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_lib_api_event_EventInterface
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
     * @since 4.0.0
     */
    function getArgument($key);

    /**
     * Getter for all arguments.
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    function getArguments();

    /**
     * Returns the EventDispatcher that dispatches this Event
     *
     * @return tubepress_lib_api_event_EventDispatcherInterface
     *
     * @api
     * @since 4.0.0
     */
    function getDispatcher();

    /**
     * Gets the event's name.
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function getName();

    /**
     * @return mixed The subject of the event.
     *
     * @api
     * @since 4.0.0
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
     * @since 4.0.0
     */
    function hasArgument($key);

   /**
     * Returns whether further event listeners should be triggered.
     *
     * @return Boolean Whether propagation was already stopped for this event.
     *
     * @api
     * @since 4.0.0
     */
    function isPropagationStopped();

    /**
     * Add argument to event.
     *
     * @param string $key   Argument name.
     * @param mixed  $value Value.
     *
     * @return tubepress_lib_api_event_EventInterface
     *
     * @api
     * @since 4.0.0
     */
    function setArgument($key, $value);

    /**
     * Add arguments to an event.
     *
     * @param array $args An associative array of arguments.
     *
     * @return tubepress_lib_api_event_EventInterface
     *
     * @api
     * @since 4.0.0
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
     * @since 4.0.0
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
     * @since 4.0.0
     */
    function stopPropagation();
}