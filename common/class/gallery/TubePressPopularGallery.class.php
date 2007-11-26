<?php
class TubePressPopularGallery extends TubePressGallery {
    
    public function __construct() {
        $this->setName(TubePressGallery::popular);
        $this->setTitle("Most-viewed videos from...");
        $this->setValue(new TubePressTimeValue());
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/standardfeeds/most_viewed?time=" . $this->getValue();
    }
}
?>