<?php
class TubePressGalleryUrl {
	
	public function get(TubePressOptionsManager $tpom) {
		
		$url = "";
		
		switch ($tpom->get(TubePressGalleryOptions::MODE)) {
			
			case TubePressGallery::USER:
				$url = "users/" . $tpom->get(TubePressGalleryOptions::USER_VALUE) . "/uploads";
				break;
			
			case TubePressGallery::TOP_RATED:
				$url = "standardfeeds/top_rated?time=" . $tpom->get(TubePressGalleryOptions::TOP_RATED_VALUE);
				break;
			
			case TubePressGallery::POPULAR:
				$url = "standardfeeds/most_viewed?time=" . $tpom->get(TubePressGalleryOptions::MOST_VIEWED_VALUE);
				break;
			
			case TubePressGallery::PLAYLIST:
				$url = "playlists/" . $tpom->get(TubePressGalleryOptions::PLAYLIST_VALUE);
				break;
				
			case TubePressGallery::MOST_RESPONDED:
				$url = "standardfeeds/most_responded";
				break;
				
			case TubePressGallery::MOST_RECENT:
				$url = "standardfeeds/most_recent";
				break;
				
			case TubePressGallery::MOST_LINKED:
				$url = "standardfeeds/most_linked";
				break;
				
			case TubePressGallery::MOST_DISCUSSESD:
				$url = "standardfeeds/most_discussed";
				break;
				
			case TubePressGallery::MOBILE:
				$url = "standardfeeds/watch_on_mobile";
				break;
				
			case TubePressGallery::FAVORITES:
				$url = "users/" . $tpom->get(TubePressGalleryOptions::FAVORITES_VALUE) . "/favorites";
				break;
				
			case TubePressGallery::TAG:
				$tags = $tpom->get(TubePressGalleryOptions::TAG_VALUE);
				$tags = explode(" ", $tags);
				$url = "videos?vq="	. implode("+", $tags);
				break;
								
			default:
				$url = "standardfeeds/recently_featured";
				break;
		}
		
		return "http://gdata.youtube.com/feeds/api/$url";
	}
}
?>