<?php
class TubePressGalleryValue extends TubePressEnumValue {
   
    public function __construct($theName, $theDefault) {
        parent::__construct($theName, array(
            TubePressGallery::favorites,
            TubePressGallery::tag,
            TubePressGallery::related,
            TubePressGallery::user,
            TubePressGallery::playlist,
            TubePressGallery::featured,
            TubePressGallery::popular,
            TubePressGallery::category,
            TubePressGallery::top_rated,
            TubePressGallery::mobile
        ), $theDefault);
    }
}
?>