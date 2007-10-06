<?php
class TubePressGalleryOptions extends TubePressOptionsCategory {
    
    const currentModeName = "mode";
    
    public function __construct() {
            
        $this->title = "Which videos?";
    
        new TubePressOption(
            TubePressGalleryOptions::currentMode,
            " ", " ",
            new TubePressGalleryValue(
                TubePressGalleryOptions::currentMode,
                TubePressGallery::featured)
        );
    }
}
?>