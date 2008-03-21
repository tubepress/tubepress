<?php
class TubePressTopRatedGallery extends TubePressGallery implements TubePressHasValue {
    
    private $timeFrame;
    
    public function __construct() {
        $this->setName(TubePressGalleryValue::top_rated);
        $this->setTitle("Top rated videos from...");
        $this->timeFrame = new TubePressTimeValue(TubePressGalleryValue::top_rated);
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/standardfeeds/top_rated?time=" . $this->getValue()->getCurrentValue();
    }
	
	public function &getValue() {
	    return $this->timeFrame;
	}
}
?>