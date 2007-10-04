<?php
class TubePressSearchGallery extends TubePressGallery {
    
    public function __construct() {
        parent::__construct(TubePressGallery::search,
            "YouTube search for",
            "YouTube limits this mode to 1,000 results");
        $this->value = "stewart daily show";
    }
}
?>