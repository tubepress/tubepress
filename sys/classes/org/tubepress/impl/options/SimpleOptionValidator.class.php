<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Display',
    'org_tubepress_api_const_options_Type',
    'org_tubepress_api_options_OptionDescriptorReference',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Performs validation on option values
 */
class org_tubepress_impl_options_SimpleOptionValidator implements org_tubepress_api_options_OptionValidator
{
    const LOG_PREFIX = 'Simple Option Validator';

    /**
     * Validates an option value.
     *
     * @param string       $optionName The option name
     * @param unknown_type $candidate  The candidate option value
     *
     * @return boolean True if the option name exists and the value supplied is valid. False otherwise.
    */
    public function isValid($optionName, $candidate)
    {
	    $ioc        = org_tubepress_impl_ioc_IocContainer::getInstance();
        $odr        = $ioc->get('org_tubepress_api_options_OptionDescriptorReference');
        $descriptor = $odr->findOneByName($optionName);

        if ($descriptor === null) {

            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'No option with name %s', $optionName);
            return false;
        }
        
        return true;
    }
}
