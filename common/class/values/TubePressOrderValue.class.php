<?php
class TubePressOrderValue extends TubePressEnumValue {
    
    const relevance = "relevance";
    const views = "viewCount";
    const rating = "rating";
    const updated = "updated";
    
    public function __construct($theName) {
        
        parent::__construct($theName, array(
            TubePressOrderValue::relevance => "relevance",
            TubePressOrderValue::views => "view count",
            TubePressOrderValue::rating => "rating",
            TubePressOrderValue::updated => "last updated"
        ), TubePressOrderValue::views);
    }
}
?>