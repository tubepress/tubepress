<?php
class TubePressPlaylistGallery extends TubePressGallery {
    
    public function __construct() {
        $this->name = TubePressGallery::playlist;
        $this->title = "This playlist";
        $this->description = "Limited to 200 videos per playlist." .
			" Will usually look something like this:" .
            " D2B04665B213AE35. Copy the playlist id from the end of the " .
            "URL in your browser's address bar (while looking at a YouTube " .
            "playlist). It comes right after the 'p='. For instance: " .
            "http://youtube.com/my_playlists?p=D2B04665B213AE35";
        $this->value = "D2B04665B213AE35";
    }
}
?>