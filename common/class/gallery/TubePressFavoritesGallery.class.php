<?php
class TubePressFavoritesGallery extends TubePressGallery {
    
    public function __construct() {
        $this->setName(TubePressGallery::favorites);
        $this->setTitle("This YouTube user's \"favorites\"");
        $this->setDescription("YouTube limits this mode to the latest 500 favorites");
        $this->setValue("mrdeathgod");
    }

    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/users/"
        	. $this->getValue() . "/favorites";
    }
    
}
?>
