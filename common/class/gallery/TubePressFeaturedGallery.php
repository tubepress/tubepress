<?php
class TubePressFeaturedGallery extends TubePressGallery {
    
    public function __construct() {
        $this->name = TubePressGallery::featured;
        $this->title = "The latest \"featured\" videos on YouTube's homepage";
    }
}
?>