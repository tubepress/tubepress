<?php
class TubePressSearchGallery extends TubePressGallery implements TubePressHasValue {
    
    private $searchString;
    
    public function __construct() {
        $this->setName(TubePressGallery::tag);
        $this->setTitle("YouTube search for");
        $this->setDescription("YouTube limits this mode to 1,000 results");
        $this->searchString = new TubePressTextValue("FIXME", "stewart daily show");
    }

    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/videos?vq=" . urlencode($this->getValue());
    }
	
	public function &getValue() {
	    return $this->searchString;
	}
}
?>