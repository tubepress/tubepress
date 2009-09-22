<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
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
    private $_messageService;
    private $_optionsReference;
    private $_log;
    private $_logPrefix;
    
    public function __construct()
    {
        $this->_logPrefix = "Input Validation Service";
    }
    
    /**
     * @see org_tubepress_options_validation_InputValidationService::validate($optionName, $candidate)
    */
    public function validate($optionName, $candidate)
    {
        $this->_checkExistence($optionName);
        $this->_checkType($optionName, $candidate);
        
        switch ($optionName) {
        
        case org_tubepress_options_category_Display::THUMB_HEIGHT:
            $this->
                _integerValidation(org_tubepress_options_category_Display::THUMB_HEIGHT, 
                $candidate, 1, 90);
            break;

        case org_tubepress_options_category_Display::THUMB_WIDTH:
            $this->
                _integerValidation(org_tubepress_options_category_Display::THUMB_WIDTH, 
                $candidate, 1, 120);
            break;

        case org_tubepress_options_category_Display::RESULTS_PER_PAGE:
            $this->
                _integerValidation(org_tubepress_options_category_Display::RESULTS_PER_PAGE, 
                $candidate, 1, 50);
            break;
        }
    }
    
    private function _checkExistence($optionName)
    {
        $exists = $this->_optionsReference->isOptionName($optionName);
        if ($exists === FALSE) {
            throw new Exception(sprintf($this->_messageService->_("validation-no-such-option"), $optionName));
        }
    }
    
    private function _checkType($optionName, $candidate)
    {
        $type = $this->_optionsReference->getType($optionName);
        
        switch ($type) {
            case org_tubepress_options_Type::TEXT:
            case org_tubepress_options_Type::YT_USER:
                if (!is_string($candidate)) {
                    throw new Exception(sprintf($this->_messageService->_("validation-text"), 
                        $optionName, $candidate));
                }
                break;
            case org_tubepress_options_Type::BOOL:
                if (strcasecmp($candidate, 'true') !== 0 && strcasecmp($candidate, 'false') !== 0) {
                    throw new Exception(sprintf($this->_messageService->_("validation-bool"), 
                        $optionName, $candidate));
                }
                break;
            case org_tubepress_options_Type::INTEGRAL:
                if (intval($candidate) == 0) {
                    throw new Exception(sprintf($this->_messageService->_("validation-int-type"), 
                        $optionName, $candidate));
                }
                break;
            case org_tubepress_options_Type::MODE:
            case org_tubepress_options_Type::ORDER:
            case org_tubepress_options_Type::PLAYER:
            case org_tubepress_options_Type::PLAYER_IMPL:
            case org_tubepress_options_Type::QUALITY:
            case org_tubepress_options_Type::SAFE_SEARCH:
            case org_tubepress_options_Type::TIME_FRAME:
                $validValues = $this->_optionsReference->getValidEnumValues($type);
                if (in_array((string)$candidate, $validValues) !== TRUE) {
                    throw new Exception(sprintf($this->_messageService->_("validation-enum"),
                        $optionName, implode(", ", $validValues), $candidate
                    ));
                }
        }
    }
    
    /**
     * Validates integral values
     *
     * @param string       $name      The name of the option being updated
     * @param unknown_type $candidate The new value for this option
     * @param int          $min       The minimum (inclusive) value this option 
     *                                 can take
     * @param int          $max       The maximum (inclusive) value this option 
     *                                 can take
     *      
     * @return void
     */
    private function _integerValidation($name, $candidate, $min, $max)
    {
        if ($candidate < $min || $candidate > $max) {
            throw new Exception(sprintf($this->_messageService->_("validation-int-range"), 
                $name, $min, $max, $candidate));
        }
    }
   
    /**
     * @see org_tubepress_options_validation_InputValidationService::setMessageService($messageService)
     */
    public function setMessageService(org_tubepress_message_MessageService $messageService)
    {
        $this->_messageService = $messageService;
    }
    
    public function setOptionsReference(org_tubepress_options_reference_OptionsReference $reference)
    {
        $this->_optionsReference = $reference;
    }
    
    public function setLog(org_tubepress_log_Log $log)
    {
        $this->_log = $log;
    }
}
