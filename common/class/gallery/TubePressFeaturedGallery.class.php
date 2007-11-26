<?php
class TubePressFeaturedGallery extends TubePressGallery {
    
    public function __construct() {
        $this->setName(TubePressGallery::featured);
        $this->setTitle("The latest \"featured\" videos on YouTube's homepage");
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/standardfeeds/recently_featured";
    }
}
?>