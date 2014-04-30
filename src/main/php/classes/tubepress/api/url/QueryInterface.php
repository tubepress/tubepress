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
 * A query object.
 *
 * @package TubePress\URL
 */
interface tubepress_api_url_QueryInterface
{
    const RFC3986_ENCODING = 'RFC3986';
    const RFC1738_ENCODING = 'RFC1738';

    /**
     * Add a value to a key.  If a key of the same name has already been added,
     * the key value will be converted into an array and the new value will be
     * pushed to the end of the array.
     *
     * @param string $key   Key to add
     * @param mixed  $value Value to add to the key
     *
     * @return tubepress_api_url_QueryInterface Self.
     */
    function add($key, $value);

    /**
     * Removes all key value pairs
     *
     * @return tubepress_api_url_QueryInterface Self.
     */
    function clear();

    /**
     * Iterates over each key value pair in the collection passing them to the
     * callable. If the callable returns true, the current value from input is
     * returned into the result tubepress_api_url_QueryInterface.
     *
     * The callable must accept two arguments:
     * - (string) $key
     * - (string) $value
     *
     * @param callable $closure Evaluation function
     *
     * @return tubepress_api_url_QueryInterface
     */
    function filter($closure);

    /**
     * Get a specific key value.
     *
     * @param string $key Key to retrieve.
     *
     * @return mixed|null Value of the key or NULL
     */
    function get($key);

    /**
     * Get all keys in the collection
     *
     * @return array
     */
    function getKeys();

    /**
     * Returns whether or not the specified key is present.
     *
     * @param string $key The key for which to check the existence.
     *
     * @return bool
     */
    function hasKey($key);

    /**
     * Checks if any keys contains a certain value
     *
     * @param string $value Value to search for
     *
     * @return mixed Returns the key if the value was found FALSE if the value
     *     was not found.
     */
    function hasValue($value);

    /**
     * Returns a tubepress_api_url_QueryInterface containing all the elements of the collection after
     * applying the callback function to each one.
     *
     * The callable should accept three arguments:
     * - (string) $key
     * - (string) $value
     * - (array) $context
     *
     * The callable must return a the altered or unaltered value.
     *
     * @param callable $closure Map function to apply
     * @param array    $context Context to pass to the callable
     *
     * @return tubepress_api_url_QueryInterface.
     */
    function map($closure, array $context = array());

    /**
     * Add and merge in a tubepress_api_url_QueryInterface or array of key value pair data.
     *
     * @param tubepress_api_url_QueryInterface|array $data Associative array of key value pair data
     *
     * @return tubepress_api_url_QueryInterface Self.
     */
    function merge($data);

    /**
     * Over write key value pairs in this collection with all of the data from
     * an array or collection.
     *
     * @param array|Traversable $data Values to override over this config
     *
     * @return tubepress_api_url_QueryInterface Self.
     */
    function overwriteWith($data);

    /**
     * Remove a specific key value pair
     *
     * @param string $key A key to remove
     *
     * @return tubepress_api_url_QueryInterface Self.
     */
    function remove($key);

    /**
     * Replace the data of the object with the value of an array
     *
     * @param array $data Associative array of data
     *
     * @return tubepress_api_url_QueryInterface Self.
     */
    function replace(array $data);

    /**
     * Set a key value pair
     *
     * @param string $key   Key to set
     * @param mixed  $value Value to set
     *
     * @return tubepress_api_url_QueryInterface Self.
     */
    function set($key, $value);

    /**
     * Specify how values are URL encoded
     *
     * @param string|bool $type One of 'RFC1738', 'RFC3986', or false to disable encoding
     *
     * @return tubepress_api_url_QueryInterface Self.
     * @throws InvalidArgumentException
     */
    function setEncodingType($type);

    /**
     * @return array This query as an associative array.
     */
    function toArray();

    /**
     * Convert the query string parameters to a query string string
     *
     * @return string
     */
    function toString();

    /**
     * Alias of toString()
     *
     * @return string
     */
    function __toString();
}
