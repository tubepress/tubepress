<?php
class TubePressTopRatedGallery extends TubePressGallery {
    
    public function __construct() {
        $this->name = TubePressGallery::favorites;
        $this->title = "This YouTube user's \"favorites\"";
        $this->description = "YouTube limits this mode to the latest 500 favorites";
        $this->value = "mrdeathgod";
    }
    
}
?>