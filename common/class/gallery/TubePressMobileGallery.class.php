<?php
class TubePressMobileGallery extends TubePressGallery {
    
    public function __construct() {
        $this->setName(TubePressGalleryValue::mobile);
        $this->setTitle("Videos for mobile phones");
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/standardfeeds/watch_on_mobile";
    }
    
}
?>