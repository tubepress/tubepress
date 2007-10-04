<?php
class TubePressUserGallery extends TubePressGallery {
    
    public function __construct() {
        parent::__construct(TubePressGallery::user,
            "Videos from this YouTube user", " ");
        $this->value = "mrdeathgod";
    }
    
}
?>