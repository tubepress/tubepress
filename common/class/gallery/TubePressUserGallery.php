<?php
class TubePressUserGallery extends TubePressGallery {
    
    public function __construct() {
        $this->name = TubePressGallery::user;
        $this->title = "Videos from this YouTube user";
        $this->value = "mrdeathgod";
    }
    
}
?>