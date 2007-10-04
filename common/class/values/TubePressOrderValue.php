<?php
class TubePressOrderValue extends TubePressEnumValue {

    public function __construct() {
        $this->validValues = array(
            "relevance", "viewCount", "rating", "updated"
        );
    }
    
}
?>