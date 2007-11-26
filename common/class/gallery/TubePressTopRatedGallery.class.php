<?php
class TubePressTopRatedGallery extends TubePressGallery {
    
    public function __construct() {
        $this->setName(TubePressGallery::top_rated);
        $this->setTitle("Top rated videos from...");
        $this->setValue(new TubePressTimeValue());
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/standardfeeds/top_rated?time=" . $this->getValue();
    }
}
?>