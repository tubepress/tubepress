<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

/**
 * The master reference for TubePress options - their names, deprecated
 * names, default values, types, etc.
 *
 */
interface org_tubepress_options_reference_OptionsReference
{   
    function appliesToYouTube($optionName);
    
    function appliesToVimeo($optionName);
    
    
    /**
     * Given an option name, determine if the option can be set via a shortcode
     *
     * @param $candidateOptionName The name of the option to look up
     *
     * @return boolean True if the option can be set via a shortcode, false otherwise
     */
    function canOptionBeSetViaShortcode($optionName);

    /**
     * Get all possible option names
     *
     * @return array An array of all TubePress option names
     */
    function getAllOptionNames();

    /**
     * Determine the TubePress category of a given option. The
     *  valid option category names are defined as the class names in
     *  the org_tubepress_options_category package. Each option must
     *  fall into exactly one category
     *
     * @param string $optionName The name of the option to look up
     *
     * @return string The category name for the given option
     */
    function getCategory($optionName);
    
    /**
     * Determine the default value of a given option. Each option must
     *  have exactly one default value.
     *
     * @param string $optionName The name of the option to look up
     *
     * @return string The default value for the given option
     */
    function getDefaultValue($optionName);

    /**
     * Get all option category names. The
     *  valid option category names are defined as the class names in
     *  the org_tubepress_options_category package.
     *
     * @return array The category option names
     */
    function getOptionCategoryNames();
    
    /**
     * Get all option names in a given category
     *
     * @param string $category The name of the category to look up
     *
     * @return array The option names of the options in the given category
     */
    function getOptionNamesForCategory($category);
    
    /**
     * Determine the type of the given option. Valid ption types are
     *  defined by the constants of the org_tubepress_options_Type class.
     *  Each option must map to exactly one type.
     *
     * @param string $optionName The name of the option to look up
     *
     * @return string The type name of the given option
     */
    function getType($optionName);
    
    /**
     * Given the name of an "enum" type option, return
     *  the valid values that this option may take on.
     *
     * @param $optionName The name of the option to look up
     *
     * @return array The valid option values for the given option
     */
    function getValidEnumValues($optionName);
    
    /**
     * Given a name, determine if there is an option that has that
     * name.
     *
     * @param $candidateOptionName The name of the option to look up
     *
     * @return boolean True if an option with the given name exists, false otherwise.
     */
    function isOptionName($candidateOptionName);

    /**
     * Given an option name, determine if the option should be displayed on the
     *  TubePress options form (UI)
     *
     * @param $candidateOptionName The name of the option to look up
     *
     * @return boolean True if the option should be displayed on the options form, false otherwise
     */
    function isOptionApplicableToOptionsForm($optionName);

    /**
     * Given an option category name, determine if the category should be displayed on the
     *  TubePress options form (UI)
     *
     * @param $candidateOptionName The name of the option category to look up
     *
     * @return boolean True if the category should be displayed on the options form, false otherwise
     */
    function isOptionCategoryApplicableToOptionsForm($optionCategoryName);

    /**
     * Given an option name, determine if the option is only applicable to TubePress Pro
     *
     * @param $optionName The name of the option to look up
     *
     * @return boolean True if the option is TubePress Pro only, false otherwise
     */
    function isOptionProOnly($optionName);

    /**
     * Given an option name, determine if the option should be stored in persistent storage
     *
     * @param $candidateOptionName The name of the option to look up
     *
     * @return boolean True if the option should be stored in persistent storage, false otherwise
     */
    function shouldBePersisted($optionName);

}
