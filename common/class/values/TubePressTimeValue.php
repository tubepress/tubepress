<?php
class TubePressTimeValue implements TubePressValue {
    
    const today = "today";
    const week = "this_week";
    const month = "this_month";
    const allTime = "all_time";
    
    public function __construct() {
        $this->validValues = array(
            "today", "this_week", "this_month", "all_time"
        );
        $this->value = TubePressTimeValue::today;
    }
}

?>