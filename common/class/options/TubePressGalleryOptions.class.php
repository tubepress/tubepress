<?php
class TubePressGalleryOptions extends TubePressOptionsCategory {
    
    const mode = "mode";
    
    private $galleries;
    
    public function __construct() {
            
        $this->setTitle("Which videos?");
    
        $this->galleries = array(
            TubePressGalleryValue::top_rated => new TubePressTopRatedGallery(),
            TubePressGalleryValue::favorites => new TubePressFavoritesGallery(),
            TubePressGalleryValue::featured => new TubePressFeaturedGallery(),
            TubePressGalleryValue::mobile => new TubePressMobileGallery(),
            TubePressGalleryValue::playlist => new TubePressPlaylistGallery(),
            TubePressGalleryValue::popular => new TubePressPopularGallery(),
            TubePressGalleryValue::tag => new TubePressSearchGallery(),
            TubePressGalleryValue::user => new TubePressUserGallery(),
            TubePressGalleryValue::most_linked => new TubePressMostLinkedGallery(),
            TubePressGalleryValue::most_recent => new TubePressMostRecentGallery(),
            TubePressGalleryValue::most_discussed => new TubePressMostDiscussedGallery(),
            TubePressGalleryValue::most_responded => new TubePressMostRespondedGallery()
        );
        
        $this->setOptions(array(
            TubePressGalleryOptions::mode => new TubePressOption(
                TubePressGalleryOptions::mode,
            	" ", " ",
                new TubePressGalleryValue(TubePressGalleryOptions::mode, $this->galleries)
            )));
    }
    
    public function printForOptionsForm(HTML_Template_IT &$tpl) {

        $tpl->setVariable("OPTION_CATEGORY_TITLE", $this->getTitle());
            
        /* go through each option in the category */
        foreach($this->galleries as $gallery) {             
            $tpl->setVariable("OPTION_TITLE", $gallery->getTitle());
            $tpl->setVariable("OPTION_DESC", $gallery->getDescription());
            $tpl->setVariable("OPTION_NAME", $gallery->getName());
            
            if ($this->get(TubePressGalleryOptions::mode)->getValue()->getCurrentValue() == $gallery->getName()) {
                $tpl->setVariable("OPTION_SELECTED", "CHECKED");
            }
            
            $tpl->parse("galleryType");
            
            if ($gallery instanceof TubePressHasValue) {
                $gallery->getValue()->printForOptionsPage($tpl);
            }
            $tpl->parse("optionRow");
        }
        $tpl->parse("optionCategory");
    }
    
    public function getGallery($galleryName) {
    	return $this->galleries[$galleryName];
    }
}
?>