<?php
class TubePressPopularGallery extends TubePressGallery implements TubePressHasValue {
    
    private $timeframe;
    
    public function __construct() {
        $this->setName(TubePressGallery::popular);
        $this->setTitle("Most-viewed videos from...");
        $this->timeframe = new TubePressTimeValue(TubePressGallery::popular);
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/standardfeeds/most_viewed?time=" . $this->getValue()->getCurrentValue();
    }
    
	public function &getValue() {
	    return $this->timeframe;
	}

}
?>