<?php

class TubePressValidator
{
	public function validate($optionName, $candidate)
	{
		switch ($optionName) {
			case TubePressDisplayOptions::THUMB_HEIGHT:
				TubePressValidator::_integerValidation(TubePressDisplayOptions::THUMB_HEIGHT, $candidate, 1, 90);
				break;
			case TubePressDisplayOptions::THUMB_WIDTH:
				TubePressValidator::_integerValidation(TubePressDisplayOptions::THUMB_WIDTH, $candidate, 1, 120);
				break;
			case TubePressDisplayOptions::RESULTS_PER_PAGE:
				TubePressValidator::_integerValidation(TubePressDisplayOptions::RESULTS_PER_PAGE, $candidate, 1, 50);
				break;
		}
	}

	private function _orderValidation($name, $candidate) {
		if (!in_array($candidate, array("relevance", "viewCount", "rating", "updated"))) {
			throw new Exception(sprintf(TpMsg::_("validation-order"), $name, $candidate));
		}
	}
	
	private function _textValidation($name, $candidate) {
	 	if (!is_string($candidate)) {
        	throw new Exception(sprintf(TpMsg::_("validation-text"), $name, $candidate));
        }
	}
	
	private function _timeFrameValidation($name, $candidate) {
		if (!in_array($candidate, array("today", "this_week", "this_month", "all_time"))) {
			throw new Exception(sprintf(TpMsg::_("validation-time"), $name, $candidate));
		}
	}
	
	private function _integerValidation($name, $candidate, $min, $max) {
		
		if (intval($candidate) == 0) {
			throw new Exception(sprintf(TpMsg::_("validation-int-type"), $name, $candidate));
		}
		
		if ($candidate < $min || $candidate > $max) {
			throw new Exception(sprintf(TpMsg::_("validation-int-range"), $name, $min, $max, $candidate));
		}
	}
}
?>