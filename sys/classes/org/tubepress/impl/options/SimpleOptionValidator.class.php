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
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_options_OptionsReference',
));

/**
 * Performs validation on option values
 */
class org_tubepress_impl_options_SimpleOptionValidator implements org_tubepress_api_options_OptionValidator
{
    /**
     * Validates an option value. Will throw an Exception if validation
     * fails.
     *
     * @param string       $optionName The option name
     * @param unknown_type $candidate  The candidate option value
     *
     * @return void
    */
    public function validate($optionName, $candidate)
    {
	$ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $messageService = $ioc->get('org_tubepress_api_message_MessageService');
        
        /* does this option name even exist? */
        self::_checkExistence($optionName, $messageService);

        /* is the value given of the right type? */
        self::_checkType($optionName, $candidate, $messageService);

        /* perform any custom validation */
        self::_customValidation($optionName, $candidate, $messageService);
    }

    /**
     * Performs "one off" validation for options
     *
     * @param string  $optionName The name of the option to validate
     * @param unknown $candidate  The value of the option to validate
     *
     * @return void
     */
    private static function _customValidation($optionName, $candidate, org_tubepress_api_message_MessageService $messageService)
    {
        switch ($optionName) {

        /* YouTube limits us to 50 per page */
        case org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE:
            self::_checkIntegerRange(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE, $candidate, 1, 50, $messageService);
            break;

        case org_tubepress_api_const_options_names_Display::THEME:
            if (strpos($candidate, '..') !== false) {
                throw new Exception($messageService->_('validation-no-dots-in-path'));
            }
            break;
        }
    }

    /**
     * Verifies if the given option name exists
     *
     * @param string $optionName The option name to check
     *
     * @return void
     */
    private static function _checkExistence($optionName, org_tubepress_api_message_MessageService $messageService)
    {
        if (org_tubepress_impl_options_OptionsReference::isOptionName($optionName) === false) {
            throw new Exception(sprintf($messageService->_('validation-no-such-option'), $optionName));
        }
    }

    /**
     * Checks if the option value has the right type
     *
     * @param string  $optionName The name of the option to validate
     * @param unknown $candidate  The value of the option to validate
     *
     * @return void
     */
    private static function _checkType($optionName, $candidate, org_tubepress_api_message_MessageService $messageService)
    {
        $type = org_tubepress_impl_options_OptionsReference::getType($optionName);

        switch ($type) {
        case org_tubepress_api_const_options_Type::TEXT:
        case org_tubepress_api_const_options_Type::YT_USER:
        case org_tubepress_api_const_options_Type::PLAYLIST:
            if (!is_string($candidate)) {
                throw new Exception(sprintf($messageService->_('validation-text'), $optionName, $candidate));
            }
            break;

        case org_tubepress_api_const_options_Type::BOOL:
            if (strcasecmp((string)$candidate, '1') !== 0 && strcasecmp((string)$candidate, '') !== 0) {
                throw new Exception(sprintf($messageService->_('validation-bool'), $optionName, $candidate));
            }
            break;

        case org_tubepress_api_const_options_Type::INTEGRAL:
            if (intval($candidate) == 0 && $optionName != org_tubepress_api_const_options_names_Display::DESC_LIMIT
                && $optionName !== org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP) {
                throw new Exception(sprintf($messageService->_('validation-int-type'), $optionName, $candidate));
            }
            break;

        case org_tubepress_api_const_options_Type::ORDER:
        case org_tubepress_api_const_options_Type::PLAYER:
        case org_tubepress_api_const_options_Type::PLAYER_IMPL:
        case org_tubepress_api_const_options_Type::SAFE_SEARCH:
        case org_tubepress_api_const_options_Type::TIME_FRAME:
            $validValues = org_tubepress_impl_options_OptionsReference::getValidEnumValues($type);
            if (in_array((string)$candidate, $validValues) !== true) {
                throw new Exception(sprintf($messageService->_('validation-enum'), $optionName, implode(', ', $validValues), $candidate));
            }
            break;

        case org_tubepress_api_const_options_Type::COLOR:
            //implement me please
            break;
        }
    }

    /**
     * Checks the range of integral values
     *
     * @param string       $name      The name of the option being validated
     * @param unknown_type $candidate The new value for this option
     * @param int          $min       The minimum (inclusive) value this option 
     *                                 can take
     * @param int          $max       The maximum (inclusive) value this option 
     *                                 can take
     *      
     * @return void
     */
    private static function _checkIntegerRange($name, $candidate, $min, $max, org_tubepress_api_message_MessageService $messageService)
    {
        if ($candidate < $min || $candidate > $max) {
            throw new Exception(sprintf($messageService->_('validation-int-range'), $name, $min, $max, $candidate));
        }
    }
}
