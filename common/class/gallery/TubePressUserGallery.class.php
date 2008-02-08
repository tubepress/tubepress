<?php
class TubePressUserGallery extends TubePressGallery implements TubePressHasValue {
    
    private $user;
    
    public function __construct() {
        $this->setName(TubePressGallery::user);
        $this->setTitle("Videos from this YouTube user");
        $this->user = new TubePressTextValue("FIXME", "3hough");
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/users/" . $this->getValue() . "uploads";
    }
	
	public function &getValue() {
	    return $this->user;
	}
}
?>