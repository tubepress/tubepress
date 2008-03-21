<?php
class TubePressMostLinkedGallery extends TubePressGallery {

    public function __construct() {
        $this->setName(TubePressGalleryValue::most_linked);
        $this->setTitle("Most-linked videos");
    }
     
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/standardfeeds/most_linked";
    }
}
?>
