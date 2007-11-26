<?php
class TubePressMobileGallery extends TubePressGallery {
    
    public function __construct() {
        $this->setName(TubePressGallery::mobile);
        $this->setTitle("Videos for mobile phones");
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/standardfeeds/watch_on_mobile";
    }
    
}
?>