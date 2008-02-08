<?php
class TubePressTimeValue extends TubePressEnumValue {
    
    const today = "today";
    const week = "this_week";
    const month = "this_month";
    const allTime = "all_time";
    
    public function __construct($theName) {

        parent::__construct($theName, array(
            TubePressTimeValue::today => "today",
            TubePressTimeValue::week => "this week",
            TubePressTimeValue::month => "this month",
            TubePressTimeValue::allTime => "all time"
        ), TubePressTimeValue::week);
    }
}

?>