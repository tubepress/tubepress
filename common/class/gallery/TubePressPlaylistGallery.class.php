<?php
class TubePressPlaylistGallery extends TubePressGallery implements TubePressHasValue {
    
    private $playlistId;
    
    public function __construct() {
        $this->setName(TubePressGalleryValue::playlist);
        $this->setTitle("This playlist");
        $this->setDescription("Limited to 200 videos per playlist." .
			" Will usually look something like this:" .
            " D2B04665B213AE35. Copy the playlist id from the end of the " .
            "URL in your browser's address bar (while looking at a YouTube " .
            "playlist). It comes right after the 'p='. For instance: " .
            "http://youtube.com/my_playlists?p=D2B04665B213AE35");
        $this->playlistId = new TubePressTextValue(TubePressGalleryValue::playlist . "Value", "D2B04665B213AE35");
    }
    
    protected final function getRequestURL() {
        return "http://gdata.youtube.com/feeds/api/playlists/" . $this->getValue()->getCurrentValue();
    }
	
	public function &getValue() {
	    return $this->playlistId;
	}
}
?>
