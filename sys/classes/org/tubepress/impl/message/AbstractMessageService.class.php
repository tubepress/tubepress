<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php' ;
tubepress_load_classes(array('org_tubepress_api_message_MessageService'));

/**
 * Shared message functionality for org_tubepress_api_message_MessageService implementations.
 * This class basically provides one additional layer of abstraction between
 * the code and the actual message in the .pot files.
 */
abstract class org_tubepress_impl_message_AbstractMessageService implements org_tubepress_api_message_MessageService
{
    private $_msgs = array(
        'options-page-title'       => 'TubePress Options',
        'options-page-save-button' => 'Save',
        'options-page-intro-text'  => 'Set default options for the plugin. Each option here can be overridden on a per page/post basis with TubePress shortcodes. See the <a href="http://tubepress.org/documentation">documentation</a> for more info. An asterisk (*) next to an option indicates it\'s only available with <a href="http://tubepress.org/features">TubePress Pro</a>.',
        'options-page-options-filter' => 'Only show options applicable to...',

        'options-category-title-output'   => 'Which videos?',
        'options-category-title-display'  => 'Appearance',
        'options-category-title-embedded' => 'Embedded Player',
        'options-category-title-meta'     => 'Meta Display',
        'options-category-title-feed'     => 'Provider Feed',
        'options-category-title-advanced' => 'Advanced',

        'options-title-top_rated'           => 'Top rated videos from...',
        'options-title-favorites'           => 'This YouTube user\'s "favorites"',
        'options-title-recently_featured'   => 'The latest "featured" videos on YouTube\'s homepage',
        'options-title-mobile'              => 'Videos for mobile phones',
        'options-title-playlist'            => 'This playlist',
        'options-desc-playlist'             => 'Limited to 200 videos per playlist. Will usually look something like this: D2B04665B213AE35. Copy the playlist id from the end of the URL in your browser\'s address bar (while looking at a YouTube playlist). It comes right after the "p=". For instance: http://youtube.com/my_playlists?p=D2B04665B213AE35',
        'options-title-most_viewed'         => 'Most-viewed videos from',
        'options-title-youtubeTopFavorites' => 'Videos most frequently flagged as favorites from...',
        'options-title-most_recent'         => 'Most-recently added videos',
        'options-title-most_discussed'      => 'Most-discussed videos',
        'options-title-most_responded'      => 'Most-responded to videos',
        'options-title-views'               => 'Views',
        'options-title-tag'                 => 'YouTube search for...',
        'options-title-user'                => 'Videos from this YouTube user',
        'options-desc-tag'                  => 'YouTube limits this mode to 1,000 results',
        'options-title-vimeoUploadedBy'     => 'Videos uploaded by this Vimeo user',
        'options-title-vimeoLikes'          => 'Videos this Vimeo user likes',
        'options-title-vimeoAppearsIn'      => 'Videos this Vimeo user appears in',
        'options-title-vimeoSearch'         => 'Vimeo search for...',
        'options-title-vimeoCreditedTo'     => 'Videos credited to this Vimeo user (either appears in or uploaded by)',
        'options-title-vimeoChannel'        => 'Videos in this Vimeo channel',
        'options-title-vimeoAlbum'          => 'Videos from this Vimeo album',
        'options-title-vimeoGroup'          => 'Videos from this Vimeo group',

        'options-title-playerLocation'   => 'Play each video',
        'options-title-descriptionLimit' => 'Maximum description length',
        'options-desc-descriptionLimit'  => 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.',
        'options-title-thumbHeight'      => 'Height (px) of thumbs',
        'options-desc-thumbHeight'       => 'Default is 90',
        'options-title-thumbWidth'       => 'Width (px) of thumbs',
        'options-desc-thumbWidth'        => 'Default is 120',
        'options-title-relativeDates'    => 'Use relative dates',
        'options-desc-relativeDates'     => 'e.g. "yesterday" instead of "November 3, 1980"',
        'options-title-resultsPerPage'   => 'Videos per Page',
        'options-desc-resultsPerPage'    => 'Default is 20. Maximum is 50',
        'options-title-orderBy'          => 'Order videos by',
        'options-desc-orderBy'			 => 'Not all sort orders can be applied to all gallery types. See the <a href="http://tubepress.org/documentation">documentation</a> for more info.',
        'options-title-paginationAbove'  => 'Show pagination above thumbnails',
        'options-title-paginationBelow'  => 'Show pagination below thumbnails',
        'options-desc-paginationAbove'   => 'Only applies to galleries that span multiple pages',
        'options-desc-paginationBelow'   => 'Only applies to galleries that span multiple pages',
        'options-title-ajaxPagination'   => '<a href="http://wikipedia.org/wiki/Ajax_(programming)">Ajax</a>-enabled pagination',
        'options-title-hqThumbs'         => 'Use high-quality thumbnails',
        'options-desc-hqThumbs'			 => 'Note: this option cannot be used with the "randomize thumbnails" feature',

        'options-title-autoplay'             => 'Auto-play videos',
        'options-title-embeddedHeight'       => 'Max height (px)',
        'options-desc-embeddedHeight'        => 'Default is 350',
        'options-title-embeddedWidth'        => 'Max width (px)',
        'options-desc-embeddedWidth'         => 'Default is 425',
        'options-title-fullscreen'           => 'Allow fullscreen playback',
        'options-title-loop'                 => 'Loop',
        'options-desc-loop'                  => 'Continue playing the video until the user stops it',
        'options-title-playerColor'          => 'Main color',
        'options-desc-playerColor'           => 'Default is 999999',
        'options-title-playerHighlight'      => 'Highlight color',
        'options-desc-playerHighlight'       => 'Default is FFFFFF',
        'options-title-showRelated'          => 'Show related videos',
        'options-desc-showRelated'           => 'Toggles the display of related videos after a video finishes',
        'options-title-showInfo'             => 'Show title and rating before video starts',
        'options-title-hd'                   => 'Show videos in high definition when available',
        'options-title-playerImplementation' => 'Implementation',
        'options-desc-playerImplementation'  => 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc)',
        'options-title-theme'                => 'Theme',
        'options-desc-theme'                 => 'The TubePress theme to use for this gallery. Your themes can be found at <tt>%s</tt>, and default themes can be found at <tt>%s</tt>.',

        'options-title-author'      => 'Author',
        'options-title-category'    => 'Category',
        'options-title-description' => 'Description',
        'options-title-id'          => 'ID',
        'options-title-length'      => 'Length',
        'options-title-rating'      => 'Rating',
        'options-title-ratings'     => 'Ratings',
        'options-title-tags'        => 'Keywords',
        'options-title-title'       => 'Title',
        'options-title-uploaded'    => 'Posted',
        'options-title-url'         => 'URL',
        'options-title-views'       => 'Views',
        'options-title-likes'       => 'Likes',

        'options-title-dateFormat'           => 'Date format',
        'options-desc-dateFormat'            => 'Set the textual formatting of date information for videos. See <a href="http://us.php.net/date">date</a> for examples.',
        'options-title-debugging_enabled'    => 'Enable debugging',
        'options-desc-debugging_enabled'     => 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.',
        'options-title-keyword'              => 'Shortcode keyword',
        'options-desc-keyword'               => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.',
        'options-title-randomize_thumbnails' => 'Randomize thumbnails',
        'options-desc-randomize_thumbnails'  => 'Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature',
        'options-title-disableHttpTransportCurl'      => 'Disable <a href="http://php.net/manual/en/function.curl-exec.php">cURL</a> HTTP transport',
        'options-desc-disableHttpTransportCurl'       => 'Do not attempt to use cURL to fetch remote feeds. Leave enabled unless you know what you are doing.',
        'options-title-disableHttpTransportExtHttp'   => 'Disable <a href="http://php.net/http_request">HTTP extension</a> transport',
        'options-desc-disableHttpTransportExtHttp'    => 'Do not attempt to use the PHP HTTP extension to fetch remote feeds. Leave enabled unless you know what you are doing.',
        'options-title-disableHttpTransportFopen'     => 'Disable <a href="http://php.net/manual/en/function.fopen.php">fopen</a> HTTP transport',
        'options-desc-disableHttpTransportFopen'      => 'Do not attempt to use fopen to fetch remote feeds. Leave enabled unless you know what you are doing.',
        'options-title-disableHttpTransportFsockOpen' => 'Disable <a href="http://php.net/fsockopen">fsockopen</a> HTTP transport',
        'options-desc-disableHttpTransportFsockOpen'  => 'Do not attempt to use fsockopen to fetch remote feeds. Leave enabled unless you know what you are doing.',
        'options-title-disableHttpTransportStreams'   => 'Disable <a href="http://php.net/manual/en/intro.stream.php">PHP streams</a> HTTP transport',
        'options-desc-disableHttpTransportStreams'    => 'Do not attempt to use PHP streams to fetch remote feeds. Leave enabled unless you know what you are doing.',
        'options-title-cacheDirectory'                => 'Cache directory',
        'options-desc-cacheDirectory'                 => 'Leave blank to attempt to use system temp directory. Otherwise enter the absolute path of a writeable directory.',
        'options-title-cacheLifetimeSeconds'          => 'Cache expiration time (seconds)',
        'options-desc-cacheLifetimeSeconds'           => 'Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).',
        'options-title-cacheCleaningFactor'           => 'Cache cleaning factor',
        'options-desc-cacheCleaningFactor'            => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.',

        'options-title-thumbsPerVideo' => 'Thumbs per video',
        'options-desc-thumbsPerVideo'  => 'How many thumbnails to generate for each video. TubePress can randomly display the thumbs each time someone visits the gallery. See the "Randomize thumbnails" option under the "Appearance" tab.',

        'options-title-filter_racy'    => 'Filter "racy" content',
        'options-desc-filter_racy'     => 'Don\'t show videos that may not be suitable for minors.',
        'options-title-videoBlacklist' => 'Videos blacklist',
        'options-desc-videoBlacklist'  => 'List of video IDs that should never be displayed',
       'options-title-developerKey'    => 'YouTube API Developer Key',
        'options-desc-developerKey'    => 'YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="http://code.google.com/apis/youtube/dashboard/">here</a>. Don\'t change this unless you know what you\'re doing.',
        'options-title-cacheEnabled'   => 'Enable request cache',
        'options-desc-cacheEnabled'    => 'Store network responses locally for 1 hour. Each response is on the order of a few hundred KB, so leaving the cache enabled will significantly reduce load times for your galleries at the slight expense of freshness.',
        'options-title-embeddableOnly' => 'Only retrieve embeddable videos',
        'options-desc-embeddableOnly'  => 'Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.',
        'options-title-resultCountCap' => 'Maximum total videos to retrieve',
        'options-desc-resultCountCap'  => 'This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.',
        'options-title-vimeoKey'       => 'Vimeo API "Consumer Key"',
        'options-desc-vimeoKey'        => '<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.',
        'options-title-vimeoSecret'    => 'Vimeo API "Consumer Secret"',
        'options-desc-vimeoSecret'     => '<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.',
        'options-title-searchResultsRestrictedToUser' => 'Restrict search results to videos from this user',
        'options-desc-searchResultsRestrictedToUser'  => 'Only applies to search-based galleries',

        'player-normal'      => 'normally (at the top of your gallery)',
        'player-popup'       => 'in a popup window',
        'player-youtube'     => 'from the video\'s original YouTube page',
        'player-shadowbox'   => 'with Shadowbox',
        'player-jqmodal'     => 'with jqModal',
        'player-tinybox'     => 'with TinyBox',
        'player-fancybox'    => 'with FancyBox',
        'player-static'      => 'statically (page refreshes on each thumbnail click)',
        'player-solo'        => 'in a new window on its own',
        'player-vimeo'       => 'from the video\'s original Vimeo page',

        'order-relevance'    => 'relevance',
        'order-viewCount'    => 'view count',
        'order-rating'       => 'rating',
        'order-published'    => 'date published',
        'order-random'       => 'randomly',
        'order-position'     => 'position in a playlist',
        'order-commentCount' => 'comment count',
        'order-duration'     => 'length',
        'order-title'        => 'title',
        'order-newest'		 => 'newest',
        'order-oldest'		 => 'oldest',

        'timeFrame-today'      => 'today',
        'timeFrame-this_week'  => 'this week',
        'timeFrame-this_month' => 'this month',
        'timeFrame-all_time'   => 'all time',

        'video-author'      => 'Author',
        'video-category'    => 'Category',
        'video-description' => 'Description',
        'video-id'          => 'ID',
        'video-length'      => 'Runtime',
        'video-rating'      => 'Rating',
        'video-ratings'     => 'Ratings',
        'video-tags'        => 'Keywords',
        'video-title'       => 'Title',
        'video-uploaded'    => 'Posted',
        'video-url'         => 'URL',
        'video-views'       => 'Views',
        'video-likes'       => 'Likes',

        'validation-int-type'              => '%s can only take on integer values. You supplied %s.',
        'validation-int-range'             => '"%s" must be between "%d" and "%d". You supplied "%d".',
        'validation-text'                  => '%s must be a string. You supplied %s.',
        'validation-no-such-option'        => '"%s" is not a valid option name.',
        'validation-bool'                  => '"%s" must be either true or false. You supplied "%s".',
        'validation-enum'                  => '"%s" must be one of "%s". You supplied "%s".',
        'validation-no-dots-in-path '      => 'This option cannot contain two consecutive periods',
        'validation-ffmpeg-not-executable' => '%s is not executable or does not exist.',
        'validation-directory-not-directory' => '%s is not a directory',
        'validation-directory-not-readable' => '%s is not readable',

        'next' => 'next',
        'prev' => 'prev',

        'widget-description'           => 'Displays YouTube videos in your sidebar using TubePress',
        'widget-tagstring-description' => 'TubePress shortcode for the widget. See the <a href="http://tubepress.org/documentation"> documentation</a>.',

        'safeSearch-none'     => 'none',
        'safeSearch-moderate' => 'moderate',
        'safeSearch-strict'   => 'strict',

        'playerImplementation-provider_based'    => 'Provider default',
        'playerImplementation-longtail'   => 'JW FLV Media Player (by Longtail Video)',
    
        'no-videos-found'     => 'No matching videos',
        'search-input-button' => 'Search'
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
        return '';
    }
}
