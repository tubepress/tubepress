<?php
class TubePressUserGallery extends TubePressGallery implements TubePressHasValue {
    
    private $user;
    
    public function __construct() {
        $this->setName(TubePressGalleryValue::user);
        $this->setTitle("Videos from this YouTube user");
        $this->user = new TubePressTextValue(TubePressGalleryValue::user . "Value", "3hough");
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/users/" . $this->getValue()->getCurrentValue() . "/uploads";
    }
	
	public function &getValue() {
	    return $this->user;
	}
}
?>