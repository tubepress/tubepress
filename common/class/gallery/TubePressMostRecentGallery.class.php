<?php
class TubePressMostRecentGallery extends TubePressGallery {

    public function __construct() {
        $this->setName(TubePressGalleryValue::most_recent);
        $this->setTitle("Most recently added");
    }
     
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/standardfeeds/most_recent";
    }
}
?>
