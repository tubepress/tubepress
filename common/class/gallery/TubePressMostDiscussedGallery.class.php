<?php
class TubePressMostDiscussedGallery extends TubePressGallery {

    public function __construct() {
        $this->setName(TubePressGalleryValue::most_discussed);
        $this->setTitle("Most discussed");
    }
     
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/standardfeeds/most_discussed";
    }
}
?>
