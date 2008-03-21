<?php
class TubePressFavoritesGallery extends TubePressGallery implements TubePressHasValue {
    
    private $user;
    
    public function __construct() {
        $this->setName(TubePressGalleryValue::favorites);
        $this->setTitle("This YouTube user's \"favorites\"");
        $this->setDescription("YouTube limits this mode to the latest 500 favorites");
        $this->user = new TubePressTextValue(TubePressGalleryValue::favorites . "Value", "mrdeathgod");
    }
     
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/users/"
        	. $this->getValue()->getCurrentValue() . "/favorites";
    }
	
	public function &getValue() {
	    return $this->user;
	}
}
?>
