<?php
class TubePressPopularGallery extends TubePressGallery {
    
    public function __construct() {
        parent::__construct(TubePressGallery::popular,
            "Most-viewed videos from...",
            " ");
        $this->value = "today"; //TODO: replace this with an enum
    }
}
?>