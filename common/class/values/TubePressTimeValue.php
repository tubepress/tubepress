<?php
class TubePressTimeValue implements TubePressValue {
    
    public function __construct() {
        $this->validValues = array(
            "today", "this_week", "this_month", "all_time"
        );
    }
}

?>