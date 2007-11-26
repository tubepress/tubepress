<?php
class TubePressUserGallery extends TubePressGallery {
    
    public function __construct() {
        $this->setName(TubePressGallery::user);
        $this->setTitle("Videos from this YouTube user");
        $this->setValue("mrdeathgod");
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/users/" . $this->getValue() . "uploads";
    }
}
?>