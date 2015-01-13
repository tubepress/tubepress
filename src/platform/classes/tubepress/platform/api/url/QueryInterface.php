<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 *
 *
 *
 * This is based on Guzzle, whose copyright follows:
 *
 * Copyright (c) 2014 Michael Dowling, https://github.com/mtdowling <mtdowling@gmail.com>
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * A query object.
 *
 * @package TubePress\URL
 *
 * @api
 * @since 4.0.0
 */
interface tubepress_platform_api_url_QueryInterface
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
     * @return tubepress_platform_api_url_QueryInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function add($key, $value);

    /**
     * Removes all key value pairs
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function clear();

    /**
     * Iterates over each key value pair in the collection passing them to the
     * callable. If the callable returns true, the current value from input is
     * returned into the result tubepress_platform_api_url_QueryInterface.
     *
     * The callable must accept two arguments:
     * - (string) $key
     * - (string) $value
     *
     * @param callable $closure Evaluation function
     *
     * @return tubepress_platform_api_url_QueryInterface
     *
     * @api
     * @since 4.0.0
     */
    function filter($closure);

    /**
     * Prevent any modifications to this query.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    function freeze();

    /**
     * Get a specific key value.
     *
     * @param string $key Key to retrieve.
     *
     * @return mixed|null Value of the key or NULL
     *
     * @api
     * @since 4.0.0
     */
    function get($key);

    /**
     * Get all keys in the collection
     *
     * @return array
     *
     * @api
     * @since 4.0.0
     */
    function getKeys();

    /**
     * Returns whether or not the specified key is present.
     *
     * @param string $key The key for which to check the existence.
     *
     * @return bool
     *
     * @api
     * @since 4.0.0
     */
    function hasKey($key);

    /**
     * Checks if any keys contains a certain value
     *
     * @param string $value Value to search for
     *
     * @return mixed Returns the key if the value was found FALSE if the value
     *     was not found.
     *
     * @api
     * @since 4.0.0
     */
    function hasValue($value);

    /**
     * @return bool True if this query is frozen, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    function isFrozen();

    /**
     * Returns a tubepress_platform_api_url_QueryInterface containing all the elements of the collection after
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
     * @return tubepress_platform_api_url_QueryInterface.
     *
     * @api
     * @since 4.0.0
     */
    function map($closure, array $context = array());

    /**
     * Add and merge in a tubepress_platform_api_url_QueryInterface or array of key value pair data.
     *
     * @param tubepress_platform_api_url_QueryInterface|array $data Associative array of key value pair data
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function merge($data);

    /**
     * Over write key value pairs in this collection with all of the data from
     * an array or collection.
     *
     * @param array|Traversable $data Values to override over this config
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function overwriteWith($data);

    /**
     * Remove a specific key value pair
     *
     * @param string $key A key to remove
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function remove($key);

    /**
     * Replace the data of the object with the value of an array
     *
     * @param array $data Associative array of data
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function replace(array $data);

    /**
     * Set a key value pair
     *
     * @param string $key   Key to set
     * @param mixed  $value Value to set
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     *
     * @api
     * @since 4.0.0
     */
    function set($key, $value);

    /**
     * Specify how values are URL encoded
     *
     * @param string|bool $type One of 'RFC1738', 'RFC3986', or false to disable encoding
     *
     * @return tubepress_platform_api_url_QueryInterface Self.
     * @throws InvalidArgumentException
     *
     * @api
     * @since 4.0.0
     */
    function setEncodingType($type);

    /**
     * @return array This query as an associative array.
     *
     * @api
     * @since 4.0.0
     */
    function toArray();

    /**
     * Convert the query string parameters to a query string string
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function toString();

    /**
     * Alias of toString()
     *
     * @return string
     *
     * @api
     * @since 4.0.0
     */
    function __toString();
}
