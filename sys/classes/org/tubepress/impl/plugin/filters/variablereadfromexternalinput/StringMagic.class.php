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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_impl_plugin_filters_AbstractStringMagicFilter'
));

/**
 * Performs filtering on potentially malicious or typo'd string input.
 */
class org_tubepress_impl_plugin_filters_variablereadfromexternalinput_StringMagic extends org_tubepress_impl_plugin_filters_AbstractStringMagicFilter
{

    /**
     * Applied to a single option name/value pair as it is read from external input.
     *
     * @param string $value The option value being set.
     * @param string $name  The name of the option being set.
     *
     * @return unknown_type The (possibly modified) option value. May be null.
     *
     * function alter_variableReadFromExternalInput($value, $name);
     */
    function alter_variableReadFromExternalInput($value, $name)
    {
        return $this->_magic($name, $value);
    }
}
