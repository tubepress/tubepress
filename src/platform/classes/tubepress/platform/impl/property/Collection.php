<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
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
class tubepress_platform_impl_property_Collection implements tubepress_platform_api_property_CollectionInterface
{
    /**
     * @var array
     */
    private $_props = array();

    /**
     * @param string $name The property name.
     *
     * @return bool True if this object contains a property with the given name, false otherwise.
     *
     * @api
     * @since 4.0.0
     */
    public function has($name)
    {
        return array_key_exists($name, $this->_props);
    }

    /**
     * @param string $name The property name.
     *
     * @return mixed The property value. May be null.
     *
     * @throws InvalidArgumentException If no such property value exists.
     *
     * @api
     * @since 4.0.0
     */
    public function get($name)
    {
        if (!$this->has($name)) {

            throw new InvalidArgumentException('No such property: ' . $name);
        }

        return $this->_props[$name];
    }

    /**
     * @param string $name  The property name.
     * @param mixed  $value The property value.
     *
     * @return void
     *
     * @api
     * @since 4.0.0
     */
    public function set($name, $value)
    {
        $this->_props[$name] = $value;
    }

    /**
     * @param string $name The property name.
     *
     * @return bool The property value as converted to boolean.
     *
     * @throws InvalidArgumentException If no such property value exists.
     *
     * @api
     * @since 4.0.0
     */
    public function getAsBoolean($name)
    {
        return (bool) $this->get($name);
    }

    /**
     * @return array All of this object's property names.
     *
     * @api
     * @since 4.0.0
     */
    public function getAllNames()
    {
        return array_keys($this->_props);
    }
}