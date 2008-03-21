<?php
class TubePressGalleryValue extends TubePressEnumValue {
    
  	/* All valid gallery types here */
	const favorites = 	"favorites";
	const tag = 		"tag";
    const user= 		"user";
    const playlist = 	"playlist";
    const featured = 	"featured";
    const popular = 	"popular";
    const top_rated = 	"top_rated";
    const mobile = 		"mobile";
    const most_linked =	"most_linked";
    const most_recent = "most_recent";
    const most_discussed = "most_discussed";
    const most_responded = "most_responded";
    
    private $galleries;
    
    /**
     * Default constructor
     *
     * @param string $theName
     */
    public function __construct($theName, array $theGalleries) {

        parent::__construct($theName, array(
            TubePressGalleryValue::favorites,
            TubePressGalleryValue::tag,
            TubePressGalleryValue::user,
            TubePressGalleryValue::playlist,
            TubePressGalleryValue::featured,
            TubePressGalleryValue::popular,
            TubePressGalleryValue::top_rated,
            TubePressGalleryValue::mobile,
            TubePressGalleryValue::most_linked
        ), TubePressGalleryValue::top_rated);
        
        $this->galleries = $theGalleries;
    }
    
    public function updateFromOptionsPage(array $postVars) {
        
    	/* see if it's there */
    	if (array_key_exists($this->getName(), $postVars)) {
        	$this->updateManually($postVars[$this->getName()]);
        }
        
        foreach ($this->galleries as &$gallery) {
        	if ($gallery instanceof TubePressHasValue) {
        		$value =& $gallery->getValue();
        		$value->updateFromOptionsPage($postVars);
        	}
        }
    }
}

?>