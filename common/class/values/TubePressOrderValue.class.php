<?php
class TubePressOrderValue extends TubePressEnumValue {
    
    const relevance = "relevance";
    const views = "viewCount";
    const rating = "rating";
    const updated = "updated";
    
    public function __construct($theName, $theDefault) {
        
        parent::__construct($theName, array(
            TubePressOrderValue::relevance,
            TubePressOrderValue::views,
            TubePressOrderValue::rating,
            TubePressOrderValue::updated
        ), $theDefault);
    }
}
?>