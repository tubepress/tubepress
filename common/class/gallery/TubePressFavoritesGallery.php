<?php
class TubePressFavoritesGallery extends TubePressGallery {
    
    public function __construct() {
        parent::__construct(TubePressGallery::favorites,
            "Top rated videos from...",
            " "
            );
        $this->value = "today";   //TODO: use an enum for this
    }
    
}
?>