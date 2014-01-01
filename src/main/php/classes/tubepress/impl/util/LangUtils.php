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
 * PHP language utilities.
 */
class tubepress_impl_util_LangUtils
{
    public static function isAssociativeArray($candidate)
    {
        return is_array($candidate)
            && ! empty($candidate)
            && count(array_filter(array_keys($candidate),'is_string')) == count($candidate);
    }

    public static function getDefinedConstants($classOrInterface)
    {
        if (! class_exists($classOrInterface) && ! interface_exists($classOrInterface)) {

            return array();
        }

        $ref       = new ReflectionClass($classOrInterface);
        $constants = $ref->getConstants();
        $toReturn  = array();

        foreach ($constants as $name => $value) {

            if (substr($name, 0, 1) !== '_') {

                $toReturn[] = $value;
            }
        }

        return $toReturn;
    }

    //https://gist.github.com/1415653
    /**
     * Tests if an input is valid PHP serialized string.
     *
     * Checks if a string is serialized using quick string manipulation
     * to throw out obviously incorrect strings. Unserialize is then run
     * on the string to perform the final verification.
     *
     * Valid serialized forms are the following:
     * <ul>
     * <li>boolean: <code>b:1;</code></li>
     * <li>integer: <code>i:1;</code></li>
     * <li>double: <code>d:0.2;</code></li>
     * <li>string: <code>s:4:"test";</code></li>
     * <li>array: <code>a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}</code></li>
     * <li>object: <code>O:8:"stdClass":0:{}</code></li>
     * <li>null: <code>N;</code></li>
     * </ul>
     *
     * @author    Chris Smith <code+php@chris.cs278.org>, Frank Bültge <frank@bueltge.de>
     * @copyright    Copyright (c) 2009 Chris Smith (http://www.cs278.org/), 2011 Frank Bültge (http://bueltge.de)
     * @license    http://sam.zoy.org/wtfpl/ WTFPL
     * @param    string    $value    Value to test for serialized form
     * @param    mixed    $result    Result of unserialize() of the $value
     * @return    boolean            True if $value is serialized data, otherwise false
     */
    public static function isSerialized($value, &$result = null)
    {
        // Bit of a give away this one
        if (! is_string($value)) {

            return false;
        }

        // Serialized false, return true. unserialize() returns false on an
        // invalid string or it could return false if the string is serialized
        // false, eliminate that possibility.
        if ('b:0;' === $value) {

            $result = false;
            return true;
        }

        $length    = strlen($value);
        $end    = '';

        if (isset($value[0])) {

            switch ($value[0]) {

                case 's':

                    if ('"' !== $value[$length - 2]) {

                        return false;
                    }

                case 'b':
                case 'i':
                case 'd':

                    // This looks odd but it is quicker than isset()ing
                    $end .= ';';

                case 'a':
                case 'O':

                    $end .= '}';

                    if (':' !== $value[1]) {

                        return false;
                    }

                    switch ($value[2]) {

                        case 0:
                        case 1:
                        case 2:
                        case 3:
                        case 4:
                        case 5:
                        case 6:
                        case 7:
                        case 8:
                        case 9:

                            break;

                        default:

                            return false;
                    }

                case 'N':

                    $end .= ';';

                    if ($value[$length - 1] !== $end[0]) {

                        return false;
                    }

                    break;

                default:

                    return false;
            }
        }

        if (($result = @unserialize($value)) === false) {

            $result = null;

            return false;
        }

        return true;
    }
}

