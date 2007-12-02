<?php
class TubePressTimeValue extends TubePressEnumValue {
    
    const today = "today";
    const week = "this_week";
    const month = "this_month";
    const allTime = "all_time";
    
    public function __construct($theName) {

        parent::__construct($theName, array(
            TubePressTimeValue::today,
            TubePressTimeValue::week,
            TubePressTimeValue::month,
            TubePressTimeValue::allTime
        ), TubePressTimeValue::week);
    }
}

?>