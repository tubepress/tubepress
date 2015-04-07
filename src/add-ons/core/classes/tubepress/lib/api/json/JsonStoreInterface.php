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
 * A JSON store.
 *
 * @api
 * @since 4.1.0
 */
interface tubepress_lib_api_json_JsonStoreInterface
{
    /**
     * @api
     * @since 4.1.0
     *
     * @return string
     */
    function toString();

    /**
     * @api
     * @since 4.1.0
     *
     * @return stdClass
     */
    function toObject();

    /**
     * @api
     * @since 4.1.0
     *
     * @return array
     */
    function toArray();

    /**
     * Gets elements matching the given JsonPath expression.
     *
     * @param string $expr JsonPath expression
     * @param bool $unique Gets unique results or not
     *
     * @api
     * @since 4.1.0
     *
     * @return array
     */
    function get($expr, $unique = false);

    /**
     * Sets the value for all elements matching the given JsonPath expression.
     *
     * @api
     * @since 4.1.0
     *
     * @param string $expr JsonPath expression
     * @param mixed $value Value to set
     *
     * @return bool returns true if success
     */
    function set($expr, $value);

    /**
     * Adds one or more elements matching the given json path expression
     *
     * @api
     * @since 4.1.0
     *
     * @param string $parentExpr JsonPath expression to the parent
     * @param mixed $value Value to add
     * @param string $name Key name
     *
     * @return bool returns true if success
     */
    function add($parentExpr, $value, $name = '');

    /**
     * Removes all elements matching the given jsonpath expression
     *
     * @api
     * @since 4.1.0
     *
     * @param string $expr JsonPath expression
     *
     * @return bool returns true if success
     */
    function remove($expr);
}