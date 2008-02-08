<?php
class TubePressTopRatedGallery extends TubePressGallery implements TubePressHasValue {
    
    private $timeFrame;
    
    public function __construct() {
        $this->setName(TubePressGallery::top_rated);
        $this->setTitle("Top rated videos from...");
        $this->timeFrame = new TubePressTimeValue("FIXME");
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/standardfeeds/top_rated?time=" . $this->getValue();
    }
	
	public function &getValue() {
	    return $this->timeFrame;
	}
}
?>