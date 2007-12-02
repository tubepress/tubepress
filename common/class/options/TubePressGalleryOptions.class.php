<?php
class TubePressGalleryOptions extends TubePressOptionsCategory {
    
    const mode = "mode";
    
    public function __construct() {
            
        $this->setTitle("Which videos?");
    
        $this->setOptions(array(
            TubePressGalleryOptions::mode => new TubePressOption(
                TubePressGalleryOptions::mode,
            	" ", " ",
                new TubePressGalleryValue(
                    TubePressGalleryOptions::mode,
                    new TubePressPopularGallery())
            )));
    }
}
?>