<?php
class TubePressMobileGallery extends TubePressGallery {
    
    public function __construct() {
        parent::__construct(TubePressGallery::mobile,
            "Videos for mobile phones",
            " "
            );
    }
    
}
?>