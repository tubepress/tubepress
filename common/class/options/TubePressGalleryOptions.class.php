<?php
class TubePressGalleryOptions extends TubePressOptionsCategory {
    
    const mode = "mode";
    
    private $galleries;
    private $activeIndex = 0;
    
    public function __construct() {
            
        $this->setTitle("Which videos?");
    
        $this->galleries = array(
            TubePressGallery::top_rated => new TubePressTopRatedGallery(),
            TubePressGallery::favorites => new TubePressFavoritesGallery(),
            TubePressGallery::featured => new TubePressFeaturedGallery(),
            TubePressGallery::mobile => new TubePressMobileGallery(),
            TubePressGallery::playlist => new TubePressPlaylistGallery(),
            TubePressGallery::popular => new TubePressPopularGallery(),
            TubePressGallery::tag => new TubePressSearchGallery(),
            TubePressGallery::user => new TubePressUserGallery()
        );
        
        $this->setOptions(array(
            TubePressGalleryOptions::mode => new TubePressOption(
                TubePressGalleryOptions::mode,
            	" ", " ",
                TubePressGallery::popular
            )));
    }
    
    public function printForOptionsForm(HTML_Template_IT &$tpl) {

        $tpl->setVariable("OPTION_CATEGORY_TITLE", $this->getTitle());
            
        /* go through each option in the category */
        foreach($this->galleries as $gallery) {             
            $tpl->setVariable("OPTION_TITLE", $gallery->getTitle());
            $tpl->setVariable("OPTION_DESC", $gallery->getDescription());
            $tpl->setVariable("OPTION_NAME", $gallery->getName());
            
            if ($this->get(TubePressGalleryOptions::mode)->getValue() == $gallery->getName()) {
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
}
?>