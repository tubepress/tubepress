<?php
class TubePressTopRatedGallery extends TubePressGallery {
    
    public function __construct() {
        parent::__construct(TubePressGallery::favorites,
            "This YouTube user's \"favorites\"",
            "YouTube limits this mode to the latest 500 favorites"
            );
        $this->value = "mrdeathgod";
    }
    
}
?>