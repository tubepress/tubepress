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

class tubepress_impl_wordpress_SimpleWordPressFunctionWrapper implements tubepress_spi_wordpress_WordPressFunctionWrapper
{
    /**
     * Retrieves the translated string from WordPress's translate().
     *
     * @param string $message Text to translate.
     * @param string $domain  Domain to retrieve the translated text.
     *
     * @return string Translated text.
     */
    public function __($message, $domain)
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        return $message == '' ? '' : __($message, $domain);
    }

    /**
     * Use the function update_option() to update a named option/value pair to the options database table.
     * The option_name value is escaped with $wpdb->escape before the INSERT statement.
     *
     * @param string $name  Name of the option to update.
     * @param string $value The NEW value for this option name. This value can be a string, an array,
     *                      an object or a serialized value.
     *
     * @return boolean True if option value has changed, false if not or if update failed.
     */
    function update_option($name, $value)
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        return update_option($name, $value);
    }

    /**
     * A safe way of getting values for a named option from the options database table.
     *
     * @param string $name Name of the option to retrieve.
     *
     * @return mixed Mixed values for the option.
     */
    function get_option($name)
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        return get_option($name);
    }

    /**
     * A safe way of adding a named option/value pair to the options database table. It does nothing if the option already exists.
     *
     * @param string $name  Name of the option to be added. Use underscores to separate words, and do not
     *                      use uppercase—this is going to be placed into the database.
     * @param string $value Value for this option name.
     *
     * @return void
     */
    function add_option($name, $value)
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        add_option($name, $value);
    }

    /**
     * A safe way of removing a named option/value pair from the options database table.
     *
     * @param string $name Name of the option to be deleted.
     *
     * @return boolean TRUE if the option has been successfully deleted, otherwise FALSE.
     */
    function delete_option($name)
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        delete_option($name);
    }
}