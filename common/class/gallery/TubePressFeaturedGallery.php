<?php
class TubePressFeaturedGallery extends TubePressGallery {
    
    public function __construct() {
        parent::__construct(TubePressGallery::featured,
            "The latest \"featured\" videos on YouTube's homepage",
            " "
            );
    }
}
?>