<?php
class TubePressStorage {
    
    private $galleryOptions;
    private $displayOptions;
    private $metaOptions;
    private $advOptions;
    
    const version = 1551;
    
    public function __construct() {
        
        $this->galleryOptions = new TubePressGalleryOptions();
        $this->displayOptions = new TubePressDisplayOptions();
        $this->metaOptions = new TubePressMetaOptions();
        $this->advOptions = new TubePressAdvancedOptions();
    }
    
    public function getGalleryOptions() { return $this->galleryOptions; }
    public function getDisplayOptions() { return $this->displayOptions; }
    public function getMetaOptions() { return $this->metaOptions; }
    public function getAdvancedOptions() { return $this->advOptions; }
}
?>