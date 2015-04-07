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
 * ORIGINAL CODE:
 *
 * Copyright (c) 2007 Stefan Goessner (goessner.net)
 * Licensed under the MIT (MIT-LICENSE.txt) licence.
 *
 * Modified by Axel Anceau
 */

/**
 * A JSON store.
 *
 */
class tubepress_lib_impl_json_JsonStore implements tubepress_lib_api_json_JsonStoreInterface
{
    /**
     * @var array
     */
    private static $_EMPTY_ARRAY = array();

    /**
     * @var array
     */
    private $_data;

    /**
     * @var tubepress_lib_impl_json_JsonPath
     */
    private $_jsonPath;

    /**
     * @param string|array|\stdClass $data
     */
    public function __construct($data)
    {
        $this->_jsonPath = new tubepress_lib_impl_json_JsonPath();
        $this->setData($data);
    }
    /**
     * Sets JsonStore's manipulated data
     * @param string|array|stdClass $data
     */
    public function setData($data)
    {
        $this->_data = $data;

        if (is_string($this->_data)) {

            $this->_data = json_decode($this->_data, true);

        } else if (is_object($data)) {

            $this->_data = json_decode(json_encode($this->_data), true);

        } else if (!is_array($data)) {

            throw new InvalidArgumentException(sprintf('Invalid data type. Expected object, array or string, got %s', gettype($data)));
        }

        if ($this->_data === null) {

            throw new InvalidArgumentException('Unable to decode JSON');
        }
    }

    /**
     * @api
     * @since 4.1.0
     *
     * @return string
     */
    public function toString()
    {
        return json_encode($this->_data);
    }

    /**
     * @api
     * @since 4.1.0
     *
     * @return stdClass
     */
    public function toObject()
    {
        return json_decode(json_encode($this->_data));
    }

    /**
     * @api
     * @since 4.1.0
     *
     * @return array
     */
    public function toArray()
    {
        return $this->_data;
    }

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
    public function get($expr, $unique = false)
    {
        if ((($exprs = $this->_normalizedFirst($expr)) !== false) &&
            (is_array($exprs) || $exprs instanceof Traversable)
        ) {

            $values = array();

            foreach ($exprs as $expr) {

                $o    =& $this->_data;
                $keys = preg_split(

                    "/([\"'])?\]\[([\"'])?/",
                    preg_replace(array("/^\\$\[[\"']?/", "/[\"']?\]$/"), '', $expr)
                );

                for ($i = 0; $i < count($keys); $i++) {

                    $o =& $o[$keys[$i]];
                }

                $values[] = & $o;
            }

            if ($unique === true) {

                if (!empty($values) && is_array($values[0])) {

                    array_walk($values, array($this, '__callbackJsonEncode'));

                    $values = array_unique($values);

                    array_walk($values, array($this, '__callbackJsonEncodeAssoc'));

                    return array_values($values);
                }

                return array_unique($values);
            }

            return $values;
        }

        return self::$_EMPTY_ARRAY;
    }

    public function __callbackJsonEncode(&$incoming)
    {
        return json_encode($incoming);
    }

    public function __callbackJsonEncodeAssoc(&$incoming)
    {
        return json_encode($incoming, true);
    }

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
    public function set($expr, $value)
    {
        $get = $this->get($expr);

        if ($res =& $get) {

            foreach ($res as &$r) {

                $r = $value;
            }

            return true;
        }

        return false;
    }

    /**
     * Adds one or more elements matching the given json path expression
     *
     * @api
     * @since 4.1.0
     *
     * @param string $parentexpr JsonPath expression to the parent
     * @param mixed $value Value to add
     * @param string $name Key name
     *
     * @return bool returns true if success
     */
    public function add($parentexpr, $value, $name = '')
    {
        $get = $this->get($parentexpr);

        if ($parents =& $get) {

            foreach ($parents as &$parent) {

                $parent = is_array($parent) ? $parent : array();

                if ($name != '') {

                    $parent[$name] = $value;

                } else {

                    $parent[] = $value;
                }
            }

            return true;
        }

        return false;
    }

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
    public function remove($expr)
    {
        if ((($exprs = $this->_normalizedFirst($expr)) !== false) &&
            (is_array($exprs) || $exprs instanceof Traversable)
        ) {

            foreach ($exprs as &$expr) {

                $o =& $this->_data;
                $keys = preg_split(

                    "/([\"'])?\]\[([\"'])?/",
                    preg_replace(array("/^\\$\[[\"']?/", "/[\"']?\]$/"), '', $expr)
                );

                for ($i = 0; $i < count($keys) - 1; $i++) {

                    $o =& $o[$keys[$i]];
                }

                unset($o[$keys[$i]]);
            }

            return true;
        }

        return false;
    }

    private function _normalizedFirst($expr)
    {
        if ($expr == '') {

            return false;

        } else {

            if (preg_match("/^\$(\[([0-9*]+|'[-a-zA-Z0-9_ ]+')\])*$/", $expr)) {

                print('normalized: ' . $expr);
                return $expr;

            } else {

                $res = $this->_jsonPath->jsonPath($this->_data, $expr, array('resultType' => 'PATH'));
                return $res;
            }
        }
    }
}