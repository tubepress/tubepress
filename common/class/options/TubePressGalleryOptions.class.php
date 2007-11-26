<?php
class TubePressGalleryOptions extends TubePressOptionsCategory {
    
    const mode = "mode";
    
    public function __construct() {
            
        $this->setTitle("Which videos?");
    
        $this->setOptions(array(
            new TubePressOption(
                TubePressGalleryOptions::mode,
            	" ", " ",
                new TubePressGalleryValue(
                    TubePressGallery::featured)
            )));
    }
}
?>