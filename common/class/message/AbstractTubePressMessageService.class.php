<?php
/**
 * Copyright 2006, 2007, 2008, 2009 Eric D. Hough (http://ehough.com)
 * 
 * This file is part of TubePress (http://tubepress.org)
 * 
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */

/**
 * Shared message functionality for TubePressMessageService implementations.
 * This class basically provides one additional layer of abstraction between
 * the code and the actual message in the .pot files.
 */
abstract class AbstractTubePressMessageService implements TubePressMessageService
{
    private $_msgs = array(
    	"options-page-title"       => "TubePress Options",
    	"options-page-save-button" => "Save", 
    	"options-page-intro-text"  => "Set default options for the plugin. Each option here can be overridden on a per page/post basis. See the <a href=\"http://tubepress.org/documentation\">documentation</a> for more info.", 
    	"options-page-donation"    => "TubePress is free. But if you enjoy the plugin, and appreciate the hundreds of hours I've spent developing and supporting it, please consider a donation. No amount is too small. Thanks!", 
    	
    	"options-category-title-gallery"  => "Which videos?", 
    	"options-category-title-display"  => "Display Options",
    	"options-category-title-embedded" => "Embedded Player", 
    	"options-category-title-meta"     => "Meta Display", 
    	"options-category-title-advanced" => "Advanced", 
    
    	"options-gallery-title-top_rated"         => "Top rated videos from...", 
    	"options-gallery-title-favorites"         => "This YouTube user's \"favorites\"", 
    	"options-gallery-title-recently_featured" => "The latest \"featured\" videos on YouTube's homepage", 
    	"options-gallery-title-mobile"            => "Videos for mobile phones", 
    	"options-gallery-title-playlist"          => "This playlist", 
    	"options-gallery-desc-playlist"           => "Limited to 200 videos per playlist. Will usually look something like this: D2B04665B213AE35. Copy the playlist id from the end of the URL in your browser's address bar (while looking at a YouTube playlist). It comes right after the 'p='. For instance: http://youtube.com/my_playlists?p=D2B04665B213AE35", 
    	"options-gallery-title-most_viewed"       => "Most-viewed videos from", 
    	"options-gallery-title-most_linked"       => "Most-linked videos", 
    	"options-gallery-title-most_recent"       => "Most-recently added videos", 
    	"options-gallery-title-most_discussed"    => "Most-discussed videos", 
    	"options-gallery-title-most_responded"    => "Most-responded to videos", 
    	"options-gallery-title-views"             => "Views", 
    	"options-gallery-title-tag"               => "YouTube search for...", 
    	"options-gallery-title-user"              => "Videos from this YouTube user", 
    	"options-gallery-desc-tag"                => "YouTube limits this mode to 1,000 results",
        
        "options-display-title-playerLocation"   => "Play each video", 
        "options-display-title-descriptionLimit" => "Maximum description length", 
        "options-display-desc-descriptionLimit"  => "Maximum number of characters to display in video descriptions. Set to 0 for no limit.", 
        "options-display-title-thumbHeight"      => "Height (px) of thumbs", 
        "options-display-desc-thumbHeight"       => "Default (and maximum) is 90", 
        "options-display-title-thumbWidth"       => "Width (px) of thumbs", 
        "options-display-desc-thumbWidth"        => "Default (and maximum) is 120", 
        "options-display-title-relativeDates"    => "Use relative dates", 
        "options-display-desc-relativeDates"     => "e.g. \"yesterday\" instead of \"November 3, 1980\"", 
        "options-display-title-resultsPerPage"   => "Videos per Page", 
        "options-display-desc-resultsPerPage"    => "Default is 20. Maximum is 50", 
        "options-display-title-orderBy"          => "Order videos by",
     
        "options-embedded-title-autoplay"       => "Auto-play videos", 
        "options-embedded-title-border"         => "Show border", 
        "options-embedded-title-embeddedHeight" => "Max height (px)", 
        "options-embedded-desc-embeddedHeight"  => "Default is 355", 
        "options-embedded-title-embeddedWidth"  => "Max height (px)", 
        "options-embedded-desc-embeddedWidth"   => "Default is 425", 
        "options-embedded-title-genie"          => "Enhanced genie menu", 
        "options-embedded-desc-genie"           => "Show the genie menu, if present, when the mouse enters the video area (as opposed to only when the user pushes the \"menu\" button", 
        "options-embedded-title-loop"           => "Loop", 
        "options-embedded-desc-loop"            => "Continue playing the video until the user stops it", 
        "options-embedded-title-playerColor"    => "Color", 
        "options-embedded-title-showRelated"    => "Show related videos", 
        "options-embedded-desc-showRelated"     => "Toggles the display of related videos after a video finishes",
        "options-embedded-title-quality"		=> "Video quality",
        
        "options-meta-title-author"      => "Author", 
        "options-meta-title-category"    => "Category", 
        "options-meta-title-description" => "Description", 
        "options-meta-title-id"          => "ID", 
        "options-meta-title-length"      => "Length", 
        "options-meta-title-rating"      => "Rating", 
        "options-meta-title-ratings"     => "Ratings", 
        "options-meta-title-tags"        => "Tags", 
        "options-meta-title-title"       => "Title", 
        "options-meta-title-uploaded"    => "Posted", 
        "options-meta-title-url"         => "URL", 
        "options-meta-title-views"       => "Views", 
        
        "options-advanced-title-dateFormat"           => "Date format", 
        "options-advanced-desc-dateFormat"            => "Set the textual formatting of date information for videos. See <a href=\"http://us.php.net/date\">date</a> for examples.", 
        "options-advanced-title-debugging_enabled"    => "Enable debugging", 
        "options-advanced-desc-debugging_enabled"     => "If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you're not having problems with TubePress, or you're worried about revealing any details of your TubePress pages, feel free to disable the feature.",
        "options-advanced-title-filter_racy"          => "Filter \"racy\" content", 
        "options-advanced-desc-filter_racy"           => "Don't show videos that may not be suitable for minors.", 
        "options-advanced-title-keyword"              => "Trigger keyword", 
        "options-advanced-desc-keyword"               => "The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.", 
        "options-advanced-title-randomize_thumbnails" => "Randomize thumbnails", 
        "options-advanced-desc-randomize_thumbnails"  => "Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video's thumbnail randomized", 
        "options-advanced-title-clientKey"            => "YouTube API Client ID", "options-advanced-desc-clientKey" => "YouTube will use this client ID for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID <a href=\"http://code.google.com/apis/youtube/dashboard/\">here</a>. Don't change this unless you know what you're doing.", 
        "options-advanced-title-developerKey"         => "YouTube API Developer Key", 
        "options-advanced-desc-developerKey"          => "YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href=\"http://code.google.com/apis/youtube/dashboard/\">here</a>. Don't change this unless you know what you're doing.", 
        "options-advanced-title-cacheEnabled"         => "Enable request cache", 
        "options-advanced-desc-cacheEnabled"          => "Store YouTube responses locally for 1 hour. Each response is on the order of a few hundred KB, so leaving the cache enabled will significantly reduce load times for your galleries at the slight expense of freshness.",
        "options-advanced-title-nofollowLinks"        => "Add rel=nofollow to most YouTube links", 
        "options-advanced-desc-nofollowLinks"         => "Prevents search engines from indexing outbound links to youtube.com. The only exception is the link to a video's original page on YouTube.", 
        
        "player-normal-desc"      => "normally (at the top of your gallery)", 
        "player-popup-desc"       => "in a popup window",
        "player-youtube-desc"     => "from the original YouTube page", 
        "player-lightwindow-desc" => "with lightWindow (experimental)", 
        "player-greybox-desc"     => "with GreyBox (experimental)", 
        "player-shadowbox-desc"   => "with Shadowbox.js (experimental)", 
    
        "color-normal"    => "normal", 
        "color-darkgrey"  => "dark gray", 
        "color-darkblue"  => "dark blue", 
        "color-lightblue" => "light blue", 
        "color-green"     => "green", 
        "color-orange"    => "orange", 
        "color-pink"      => "pink", 
        "color-purple"    => "purple", 
        "color-red"       => "red", 
    
        "order-relevance"  => "relevance", 
        "order-views"      => "view count", 
        "order-rating"     => "rating", 
        "order-published"  => "date published", 
        "order-random"     => "randomly", 
    
        "timeframe-today"   => "today", 
        "timeframe-week"    => "this week", 
        "timeframe-month"   => "this month", 
        "timeframe-alltime" => "all time", 
    
        "video-author"      => "Author", 
        "video-category"    => "Category", 
        "video-description" => "", 
        "video-id"          => "ID", 
        "video-length"      => "", 
        "video-rating"      => "Rating", 
        "video-ratings"     => "Ratings", 
        "video-tags"        => "Tags", 
        "video-title"       => "", 
        "video-uploaded"    => "Posted", 
        "video-url"         => "URL", 
        "video-views"       => "Views", 
    
        "validation-int-type"  => "%s can only take on integer values. You supplied %s.", 
        "validation-int-range" => "%s must be between %d and %d. You supplied %d.", 
        "validation-time"      => "%s must be one of \"today\", \"this_week\", \"this_month\", \"all_time\". You supplied %s.", 
        "validation-order"     => "%s must be on of \"relevance\", \"viewCount\", \"rating\", \"updated\", \"random\". You supplied %s.", 
        "validation-text"      => "%s must be a string. You supplied %s.", 
    
        "next" => "next", 
        "prev" => "prev", 

    	"widget-description"           => "Displays YouTube videos in your sidebar using TubePress", 
        "widget-tagstring-description" => "TubePress shortcode for the widget. See the <a href=\"http://tubepress.org/documentation\"> documentation</a>.",
        
        "quality-normal"  => "Normal",
        "quality-high"    => "High",
        "quality-higher"  => "Higher",
        "quality-highest" => "Highest"
    );
    
	/**
	 * Takes a message key and provides the actual message to translate
	 *
	 * @param string $msgId The message id
	 *
	 * @return string The message translation key for gettext
	 */
	protected function _keyToMessage($msgId)
	{
	    if (array_key_exists($msgId, $this->_msgs)) {
	        return $this->_msgs[$msgId];	 
	    }
	    return "";
	}
}
