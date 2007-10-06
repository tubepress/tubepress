<?php
class TubePressStorage {
    
    private $galleryOptions;
    private $displayOptions;
    private $metaOptions;
    private $advOptions;
    
    final public $version = 1551;
    
    public function __construct() {
        
        $this->galleryOptions = new TubePressGalleryOptions();
        $this->displayOptions = new TubePressDisplayOptions();
        $this->metaOptions = new TubePressMetaOptions();
        $this->advOptions = new TubePressAdvancedOptions();
    }
}
?>