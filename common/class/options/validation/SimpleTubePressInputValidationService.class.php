<?php
/**
 * Copyright 2006, 2007, 2008 Eric D. Hough (http://ehough.com)
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
 * Holds the current options for TubePress. This is the default options,
 * usually in persistent storage somewhere, and custom options parsed
 * from a shortcode
 */
class SimpleTubePressInputValidationService implements TubePressInputValidationService
{
	private $_messageService;
	
    /**
     * Validates options before they get stored
     *
     * @param string       $optionName The name of the option being updated
     * @param unknown_type $candidate  The new value for this option
     *      
     * @return void
     */
    public function validate($optionName, $candidate)
    {
        switch ($optionName) {
        
        case TubePressDisplayOptions::THUMB_HEIGHT:
            $this->
                _integerValidation(TubePressDisplayOptions::THUMB_HEIGHT, 
                $candidate, 1, 90);
            break;

        case TubePressDisplayOptions::THUMB_WIDTH:
            $this->
                _integerValidation(TubePressDisplayOptions::THUMB_WIDTH, 
                $candidate, 1, 120);
            break;

        case TubePressDisplayOptions::RESULTS_PER_PAGE:
            $this->
                _integerValidation(TubePressDisplayOptions::RESULTS_PER_PAGE, 
                $candidate, 1, 50);
            break;
        }
    }

    /**
     * Validates order values
     *
     * @param string $name      The name of the option being updated
     * @param string $candidate The new value for this option
     * 
     * @return void
     */
    private function _orderValidation($name, $candidate)
    {
        if (!in_array($candidate, 
            array("relevance", "viewCount", "rating", "updated", "random"))) {
                throw new Exception(sprintf($this->_messageService->_("validation-order"), 
                    $name, $candidate));
        }
    }
    
    /**
     * Validates text values
     *
     * @param string       $name      The name of the option being updated
     * @param unknown_type $candidate The new value for this option
     *      
     * @return void
     */
    private function _textValidation($name, $candidate)
    {
        if (!is_string($candidate)) {
            throw new Exception(sprintf($this->_messageService->_("validation-text"), 
                $name, $candidate));
        }
    }
    
    /**
     * Validates timeframe values
     *
     * @param string       $name      The name of the option being updated
     * @param unknown_type $candidate The new value for this option
     * 
     * @return void
     */
    private function _timeFrameValidation($name, $candidate)
    {
        if (!in_array($candidate, 
            array("today", "this_week", "this_month", "all_time"))) {
                throw new Exception(sprintf($this->_messageService->_("validation-time"), 
                    $name, $candidate));
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
        
        if (intval($candidate) == 0) {
            throw new Exception(sprintf($this->_messageService->_("validation-int-type"), 
                $name, $candidate));
        }
        
        if ($candidate < $min || $candidate > $max) {
            throw new Exception(sprintf($this->_messageService->_("validation-int-range"), 
                $name, $min, $max, $candidate));
        }
    }
    
    public function setMessageService(TubePressMessageService $messageService)
    {
    	$this->_messageService = $messageService;
    }
}
