<?php
class TubePressPopularGallery extends TubePressGallery {
    
    public function __construct() {
        $this->name = TubePressGallery::popular;
        $this->title = "Most-viewed videos from...";
        $this->value = "today"; //TODO: replace this with an enum
    }
}
?>