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

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_options_validation_InputValidationService',
    'org_tubepress_options_category_Display',
    'org_tubepress_message_MessageService',
    'org_tubepress_options_reference_OptionsReference',
    'org_tubepress_log_Log'));

/**
 * Default implementation of org_tubepress_options_validation_InputValidationService
 */
class org_tubepress_options_validation_SimpleInputValidationService implements org_tubepress_options_validation_InputValidationService
{
    private $_log;
    private $_logPrefix;
    private $_messageService;
    private $_optionsReference;
    
    public function __construct()
    {
        $this->_logPrefix = "Input Validation Service";
    }
    
    /**
     * @see org_tubepress_options_validation_InputValidationService::validate($optionName, $candidate)
    */
    public function validate($optionName, $candidate)
    {
        /* does this option name even exist? */
        $this->_checkExistence($optionName);

        /* is the value given of the right type? */
        $this->_checkType($optionName, $candidate);
        
        /* perform any custom validation */
        $this->_customValidation($optionName, $candidate);
    }

    /**
     * Performs "one off" validation for options
     *
     * @param $optionName string The name of the option to validate
     * @param $candidate unknown The value of the option to validate
     *
     * @return void
     */
    private function _customValidation($optionName, $candidate)
    {
        switch ($optionName) {
        
            /* YouTube limits us to 50 per page */
            case org_tubepress_options_category_Display::RESULTS_PER_PAGE:
                $this->_checkIntegerRange(org_tubepress_options_category_Display::RESULTS_PER_PAGE, $candidate, 1, 50);
                break;
            
            case org_tubepress_options_category_Gallery::TEMPLATE:
                if (strpos($candidate, '..') !== FALSE) {
                    throw new Exception($this->_messageService->_("validation-no-dots-in-template"));
                }
                break;
        }
    }
    
    /**
     * Verifies if the given option name exists
     *
     * @param $optionName string The option name to check
     *
     * @return void
     */
    private function _checkExistence($optionName)
    {
        if ($this->_optionsReference->isOptionName($optionName) === FALSE) {
            throw new Exception(sprintf($this->_messageService->_("validation-no-such-option"), $optionName));
        }
    }
    
    /**
     * Checks if the option value has the right type
     *
     * @param $optionName string The name of the option to validate
     * @param $candidate unknown The value of the option to validate
     *
     * @return void
     */
    private function _checkType($optionName, $candidate)
    {
        $type = $this->_optionsReference->getType($optionName);
        
        switch ($type) {
            case org_tubepress_options_Type::TEXT:
            case org_tubepress_options_Type::YT_USER:
            case org_tubepress_options_Type::PLAYLIST:
                if (!is_string($candidate)) {
                    throw new Exception(sprintf($this->_messageService->_("validation-text"), 
                        $optionName, $candidate));
                }
                break;
            case org_tubepress_options_Type::BOOL:
                if (strcasecmp((string)$candidate, '1') !== 0 && strcasecmp((string)$candidate, '') !== 0) {
                    throw new Exception(sprintf($this->_messageService->_("validation-bool"), 
                        $optionName, $candidate));
                }
                break;
            case org_tubepress_options_Type::INTEGRAL:
                if (intval($candidate) == 0 && $optionName != org_tubepress_options_category_Display::DESC_LIMIT) {
                    throw new Exception(sprintf($this->_messageService->_("validation-int-type"), 
                        $optionName, $candidate));
                }
                break;
            case org_tubepress_options_Type::MODE:
            case org_tubepress_options_Type::ORDER:
            case org_tubepress_options_Type::PLAYER:
            case org_tubepress_options_Type::PLAYER_IMPL:
            case org_tubepress_options_Type::SAFE_SEARCH:
            case org_tubepress_options_Type::TIME_FRAME:
                $validValues = $this->_optionsReference->getValidEnumValues($type);
                if (in_array((string)$candidate, $validValues) !== TRUE) {
                    throw new Exception(sprintf($this->_messageService->_("validation-enum"),
                        $optionName, implode(", ", $validValues), $candidate
                    ));
                }
                break;
            case org_tubepress_options_Type::COLOR:
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
    private function _checkIntegerRange($name, $candidate, $min, $max)
    {
        if ($candidate < $min || $candidate > $max) {
            throw new Exception(sprintf($this->_messageService->_("validation-int-range"), 
                $name, $min, $max, $candidate));
        }
    }
   
    public function setMessageService(org_tubepress_message_MessageService $messageService) { $this->_messageService = $messageService; }
    public function setOptionsReference(org_tubepress_options_reference_OptionsReference $reference) { $this->_optionsReference = $reference; }
    public function setLog(org_tubepress_log_Log $log) { $this->_log = $log; }
}
