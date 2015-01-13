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
 *
 * @api
 * @since 4.0.0
 */
class tubepress_platform_impl_collection_Map implements tubepress_platform_api_collection_MapInterface
{
    /**
     * @var array
     */
    private $_props = array();

    /**
     * Removes all of the mappings from this map. The map will be empty after this call returns.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function clear()
    {
        $this->_props = array();
    }

    /**
     * Returns true if this map contains a mapping for the specified key. More formally, returns true if and only if
     * this map contains a mapping for a key k such that (key === null ? k === null : key === k).
     * (There can be at most one such mapping.)
     *
     * @param string $key Key whose presence in this map is to be tested.
     *
     * @return bool True this map contains a mapping for the specified key.
     *
     * @api
     * @since 4.0.0
     */
    public function containsKey($key)
    {
        return array_key_exists($key, $this->_props);
    }

    /**
     * Returns true if this map maps one or more keys to the specified value. More formally, returns true if and only
     * if this map contains at least one mapping to a value v such that (value === null ? v === null : value === v).
     * This operation will probably require time linear.
     *
     * @param mixed $value Value whose presence in this map is to be tested
     *
     * @return bool True if this map maps one or more keys to the specified value.
     */
    public function containsValue($value)
    {
        return in_array($value, array_values($this->_props));
    }

    /**
     * @return int Returns the number of key-value mappings in this map.
     *
     * @api
     * @since 4.0.0
     */
    public function count()
    {
        return count($this->_props);
    }

    /**
     * Returns the value to which the specified key is mapped.
     *
     * @param string $key The key to retrieve.
     *
     * @return mixed The property value. May be null.
     *
     * @throws InvalidArgumentException If no such key exists.
     *
     * @api
     * @since 4.0.0
     */
    public function get($key)
    {
        if (!$this->containsKey($key)) {

            throw new InvalidArgumentException('No such key: ' . $key);
        }

        return $this->_props[$key];
    }

    /**
     * @param string $key The property name.
     *
     * @return bool The property value as converted to boolean.
     *
     * @throws InvalidArgumentException If no such key exists.
     *
     * @api
     * @since 4.0.0
     */
    public function getAsBoolean($key)
    {
        return (bool) $this->get($key);
    }

    /**
     * @return bool True if this map contains no key-value mappings, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function isEmpty()
    {
        return count($this->_props) === 0;
    }

    /**
     * Returns a Set view of the keys contained in this map. The set is backed by the map, so changes to the map are
     * reflected in the set, and vice-versa.
     *
     * @return array The keys contained in this map.
     *
     * @api
     * @since 4.0.0
     */
    public function keySet()
    {
        return array_keys($this->_props);
    }

    /**
     * Associates the specified value with the specified key in this map. If the map previously
     * contained a mapping for the key, the old value is replaced by the specified value.
     * (A map m is said to contain a mapping for a key k if and only if m.containsKey(k) would return true.)
     *
     * @param string $name  Key with which the specified value is to be associated
     * @param mixed  $value Value to be associated with the specified key
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function put($name, $value)
    {
        $this->_props[$name] = $value;
    }

    /**
     * Removes the mapping for a key from this map if it is present. Returns the value to which this map previously
     * associated the key. The map will not contain a mapping for the specified key once the call returns.
     *
     * @param string $key Key whose mapping is to be removed from the map.
     *
     * @return mixed The previous value associated with key.
     *
     * @throws InvalidArgumentException If no such key exists.
     *
     * @api
     * @since 4.0.0
     */
    public function remove($key)
    {
        if (!$this->containsKey($key)) {

            throw new InvalidArgumentException('No such key: ' . $key);
        }

        unset($this->_props[$key]);
    }

    /**
     * Returns a Collection view of the values contained in this map. The collection is backed by the map, so changes
     * to the map are reflected in the collection, and vice-versa.
     *
     * @return array The values contained in this map
     *
     * @api
     * @since 4.0.0
     */
    public function values()
    {
        return array_values($this->_props);
    }

}