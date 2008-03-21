<?php
class TubePressMostRespondedGallery extends TubePressGallery {

    public function __construct() {
        $this->setName(TubePressGalleryValue::most_responded);
        $this->setTitle("Videos with the most responses");
    }
     
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/standardfeeds/most_responded";
    }
}
?>
