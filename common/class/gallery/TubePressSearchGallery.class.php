<?php
class TubePressSearchGallery extends TubePressGallery implements TubePressHasValue {
    
    private $searchString;
    
    public function __construct() {
        $this->setName(TubePressGalleryValue::tag);
        $this->setTitle("YouTube search for");
        $this->setDescription("YouTube limits this mode to 1,000 results");
        $this->searchString = new TubePressTextValue(TubePressGalleryValue::tag . "Value", "stewart daily show");
    }

    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/videos?vq=" . urlencode($this->getValue()->getCurrentValue());
    }
	
	public function &getValue() {
	    return $this->searchString;
	}
}
?>