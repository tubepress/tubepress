<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_util_StringUtils',
));

/**
 * Performs filtering on potentially malicious or typo'd string input.
 */
abstract class org_tubepress_impl_plugin_filters_AbstractStringMagicFilter
{
    protected function _magic($name, $value)
    {
        /** If it's an array, send each element through the filter. */
        if (is_array($value)) {

            foreach ($value as $key => $subValue) {

                $value[$key] = $this->_magic($key, $subValue);
            }

            return $value;
        }

        /** We're only interested in strings. */
        if (! is_string($value)) {

            return $value;
        }

        $toReturn = trim($value);
        $toReturn = htmlspecialchars($toReturn, ENT_NOQUOTES);
        $toReturn = org_tubepress_impl_util_StringUtils::stripslashes_deep($toReturn);
        $toReturn = $this->_booleanMagic($toReturn);

        return $toReturn;
    }

    //http://php.net/manual/en/language.types.boolean.php
    private function _booleanMagic($value)
    {
        if (strcasecmp($value, 'false') === 0) {

            return false;
        }

        if (strcasecmp($value, 'true') === 0) {

            return true;
        }

        return $value;
    }
}
