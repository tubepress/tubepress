<?php
class TubePressFavoritesGallery extends TubePressGallery {
    
    public function __construct() {
        $this->name = TubePressGallery::favorites;
        $this->title = "Top rated videos from...";
        $this->value = new TubePressTimeValue();
    }
    
}
?>