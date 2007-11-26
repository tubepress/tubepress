<?php
class TubePressSearchGallery extends TubePressGallery {
    
    public function __construct() {
        $this->setName(TubePressGallery::search);
        $this->setTitle("YouTube search for");
        $this->setDescription("YouTube limits this mode to 1,000 results");
        $this->setValue("stewart daily show");
    }

    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/videos?vq=" . urlencode($this->getValue());
    }
}
?>