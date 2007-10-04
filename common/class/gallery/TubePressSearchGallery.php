<?php
class TubePressSearchGallery extends TubePressGallery {
    
    public function __construct() {
        $this->name = TubePressGallery::search;
        $this->title = "YouTube search for";
        $this->description = "YouTube limits this mode to 1,000 results";
        $this->value = "stewart daily show";
    }
}
?>