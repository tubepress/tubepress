<?php
class TubePressOrderValue extends TubePressEnumValue {
    
    const relevance = "relevance";
    const views = "viewCount";
    const rating = "rating";
    const updated = "updated";
    
    public function __construct($theName, $theDefault) {
        $this->validValues = array(
            TubePressOrderValue::relevance,
            TubePressOrderValue::views,
            TubePressOrderValue::rating,
            TubePressOrderValue::updated
        );
        $this->name = $theName;
        $this->setValue($theDefault);
    }
    
    
    
}
?>