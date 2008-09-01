<?php
class TpMsg {

	/**
	 * Retrieves a message for TubePress
	 *
	 * @param string $msgId The message ID
	 *
	 * @return string The corresponding message, or "" if not found
	 */
	public static function _($msgId)
	{
	    $message = TpMsg::_keyToMessage($msgId);
	    return $message == "" ? "" : 
		    __($message, "tubepress");
	}

	/**
	 * One extra layer of abstraction for message bundling
	 *
	 * @param string $msgId The message id
	 *
	 * @return string The message translation key for gettext
	 */
	private static function _keyToMessage($msgId)
	{
		switch ($msgId) {
			 
			case "options-page-title":
				return "TubePress Options";
					
			case "options-page-save-button":
				return "Save";

			case "options-page-intro-text":
				return "Set default options for the plugin. Each option here can be overridden on a per page/post basis. See the <a href=\"http://code.google.com/p/tubepress/wiki/Documentation\">documentation</a> for more info.";

			case "options-page-donation":
				return "TubePress is free. But if you enjoy the plugin, and appreciate the hundreds of hours I've spent developing and supporting it, please consider a donation. No amount is too small. Thanks!";
				
			case "options-category-title-gallery":
				return "Which videos?";

			case "options-gallery-title-top_rated":
				return "Top rated videos from...";

			case "options-gallery-title-favorites":
			    return "This YouTube user's \"favorites\"";

			case "options-gallery-title-featured":
			    return "The latest \"featured\" videos on YouTube's homepage";

			case "options-gallery-title-mobile":
			    return "Videos for mobile phones";

			case "options-gallery-title-playlist":
			    return "This playlist";

			case "options-gallery-desc-playlist":
			    return "Limited to 200 videos per playlist. Will usually look something like this: D2B04665B213AE35. Copy the playlist id from the end of the URL in your browser's address bar (while looking at a YouTube playlist). It comes right after the 'p='. For instance: http://youtube.com/my_playlists?p=D2B04665B213AE35";

			case "options-gallery-title-most_viewed":
			    return "Most-viewed videos from";

			case "options-gallery-title-most_linked":
			    return "Most-linked videos";

			case "options-gallery-title-most_recent":
			    return "Most-recently added videos";

			case "options-gallery-title-most_discussed":
			    return "Most-discussed videos";

			case "options-gallery-title-most_responded":
			    return "Most-responded to videos";

			case "options-gallery-title-views":
			    return "Views";

			case "options-gallery-title-tag":
			    return "YouTube search for...";

			case "options-gallery-title-user":
			    return "Videos from this YouTube user";

			case "options-gallery-desc-tag":
			    return "YouTube limits this mode to 1,000 results";

			case "options-category-title-display":
			    return "Display Options";

			case "options-display-title-playerLocation":
			    return "Play each video";

			case "options-display-title-descriptionLimit":
				return "Maximum description length";
				
			case "options-display-desc-descriptionLimit":
				return "Maximum number of characters to display in video descriptions. Set to 0 for no limit.";
			    
			case "options-display-title-thumbHeight":
			    return "Height (px) of thumbs";

			case "options-display-desc-thumbHeight":
			    return "Default (and maximum) is 90";

			case "options-display-title-thumbWidth":
			    return "Width (px) of thumbs";

			case "options-display-desc-thumbWidth":
			    return "Default (and maximum) is 120";

			case "options-display-title-relativeDates":
				return "Use relative dates";
				
			case "options-display-desc-relativeDates":
				return "e.g. \"yesterday\" instead of \"November 3, 1980\"";
			    
			case "options-display-title-resultsPerPage":
			    return "Videos per Page";

			case "options-display-desc-resultsPerPage":
			    return "Default is 20. Maximum is 50";

			case "options-display-title-orderBy":
			    return "Order videos by";

			case "options-category-title-embedded":
			    return "Embedded Player";

			case "options-embedded-title-autoplay":
			    return "Auto-play videos";

			case "options-embedded-title-border":
			    return "Show border";

			case "options-embedded-title-embeddedHeight":
			    return "Max height (px)";

			case "options-embedded-desc-embeddedHeight":
			    return "Default is 355";

			case "options-embedded-title-embeddedWidth":
			    return "Max height (px)";

			case "options-embedded-desc-embeddedWidth":
			    return "Default is 425";

			case "options-embedded-title-genie":
			    return "Enhanced genie menu";

			case "options-embedded-desc-genie":
			    return "Show the genie menu, if present, when the mouse enters the video area (as opposed to only when the user pushes the \"menu\" button";

			case "options-embedded-title-loop":
			    return "Loop";

			case "options-embedded-desc-loop":
			    return "Continue playing the video until the user stops it";

			case "options-embedded-title-playerColor":
			    return "Color";

			case "options-embedded-title-showRelated":
			    return "Show related videos";

			case "options-embedded-desc-showRelated":
			    return "Toggles the display of related videos after a video finishes";

			case "options-category-title-meta":
			    return "Meta Display";

			case "options-meta-title-author":
			    return "Author";

			case "options-meta-title-category":
			    return "Category";

			case "options-meta-title-description":
			    return "Description";

			case "options-meta-title-id":
			    return "ID";

			case "options-meta-title-length":
			    return "Length";

			case "options-meta-title-rating":
			    return "Rating";

			case "options-meta-title-ratings":
			    return "Ratings";

			case "options-meta-title-tags":
			    return "Tags";

			case "options-meta-title-title":
			    return "Title";

			case "options-meta-title-uploaded":
			    return "Posted";

			case "options-meta-title-url":
			    return "URL";

			case "options-meta-title-views":
			    return "Views";

			case "options-category-title-advanced":
			    return "Advanced";

			case "options-advanced-title-dateFormat":
			    return "Date format";

			case "options-advanced-desc-dateFormat":
			    return "Set the textual formatting of date information for videos. See <a href=\"http://us.php.net/date\">date</a> for examples.";

			case "options-advanced-title-debugging_enabled":
			    return "Enable debugging";

			case "options-advanced-desc-debugging_enabled":
			    return "If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you're not having problems with TubePress, or you're worried about revealing any details of your TubePress pages, feel free to disable the feature.";

			case "options-advanced-title-filter_racy":
			    return "Filter \"racy\" content";

			case "options-advanced-desc-filter_racy":
			    return "Don't show videos that may not be suitable for minors.";

			case "options-advanced-title-keyword":
			    return "Trigger keyword";

			case "options-advanced-desc-keyword":
			    return "The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.";

			case "options-advanced-title-randomize_thumbnails":
			    return "Randomize thumbnails";

			case "options-advanced-desc-randomize_thumbnails":
			    return "Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video's thumbnail randomized";

			case "options-advanced-title-clientKey":
				return "YouTube API Client ID";

			case "options-advanced-desc-clientKey":
				return "YouTube will use this client ID for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID <a href=\"http://code.google.com/apis/youtube/dashboard/\">here</a>. Don't change this unless you know what you're doing.";
			    
			case "options-advanced-title-developerKey":
				return "YouTube API Developer Key";

			case "options-advanced-desc-developerKey":
				return "YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href=\"http://code.google.com/apis/youtube/dashboard/\">here</a>. Don't change this unless you know what you're doing.";
				
			case "player-normal-desc":
			    return "normally (at the top of your gallery)";

			case "player-popup-desc":
			    return "in a popup window";

			case "player-youtube-desc":
			    return "from the original YouTube page";

			case "player-lightwindow-desc":
			    return "with lightWindow (experimental)";

			case "player-greybox-desc":
			    return "with GreyBox (experimental)";

			case "player-shadowbox-desc":
			    return "with Shadowbox.js (experimental)";

			case "color-normal":
			    return "normal";

			case "color-darkgrey":
			    return "dark gray";

			case "color-darkblue":
			    return "dark blue";

			case "color-lightblue":
			    return "light blue";

			case "color-green":
			    return "green";

			case "color-orange":
			    return "orange";

			case "color-pink":
			    return "pink";

			case "color-purple":
			    return "purple";

			case "color-red":
			    return "red";

			case "order-relevance":
			    return "relevance";

			case "order-views":
			    return "view count";

			case "order-rating":
			    return "rating";

			case "order-updated":
			    return "date updated";

			case "timeframe-today":
			    return "today";

			case "timeframe-week":
			    return "this week";

			case "timeframe-month":
			    return "this month";

			case "timeframe-alltime":
			    return "all time";

			case "video-author":
			    return "Author";

			case "video-category":
			    return "Category";

			case "video-description":
			    return "";

			case "video-id":
			    return "ID";

			case "video-length":
			    return "";

			case "video-rating":
			    return "Rating";

			case "video-ratings":
			    return "Ratings";

			case "video-tags":
			    return "Tags";

			case "video-title":
			    return "";

			case "video-uploaded":
			    return "Posted";

			case "video-url":
			    return "URL";

			case "video-views":
			    return "Views";

			case "validation-int-type":
			    return "%s can only take on integer values. You supplied %s.";

			case "validation-int-range":
			    return "%s must be between %d and %d. You supplied %d.";

			case "validation-time":
			    return "%s must be one of \"today\", \"this_week\", \"this_month\", \"all_time\". You supplied %s.";

			case "validation-order":
			    return "%s must be on of \"relevance\", \"viewCount\", \"rating\", \"updated\". You supplied %s.";

			case "validation-text":
			    return "%s must be a string. You supplied %s.";
			    
			case "next":
			    return "next";
			    
			case "prev":
			    return "prev";
			    
			case "widget-description":
				return "Displays YouTube videos in your sidebar using TubePress";
				
			case "widget-tagstring-description":
				return "Tag string. See the <a href=\"http://code.google.com/p/tubepress/wiki/TubePressWidget\">widget documentation</a>.";
			    
			default:
			    return "";
		}
		 
	}
}
