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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_options_OptionDescriptorReference',
    'org_tubepress_api_options_OptionDescriptor'
));

/**
 * Holds all the option descriptors for TubePress. This implementation just holds them in memory.
 */
class org_tubepress_impl_options_DefaultOptionDescriptorReference implements org_tubepress_api_options_OptionDescriptorReference
{
    /** Provides fast lookup by name. */
    private $_nameToOptionDescriptorMap;

    /** All option descriptors. */
    private $_optionDescriptors;

    public function __construct()
    {
        /* build all the option descriptors. */
        $this->_optionDescriptors = self::_buildAllOptionDescriptors();

        /* save each option descriptor in a keyed map */
        $this->_nameToOptionDescriptorMap = array();

        foreach ($options as $option) {

            $this->_nameToOptionDescriptorMap[$option->getName()] = $option;
        }
    }

    public function findAll()
    {
        return $this->_optionDescriptors;
    }

    function findOneByName($name)
    {
        return $this->_nameToOptionDescriptorMap[$name];
    }

    private static function _buildAllOptionDescriptors()
    {
        $builder = new org_tubepress_impl_options_DefaultOptionsDescriptorReferenceBuilder();

        return array(

            $builder->setName(org_tubepress_api_const_options_names_Advanced::CACHE_CLEAN_FACTOR)
                    ->setDefaultValue(20)
                    ->setLabel('Cache cleaning factor')
                    ->setDescription('If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.')
                    ->setNonNegativeInteger()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::CACHE_DIR)
                    ->setLabel('Cache directory')
            		->setDescription('Leave blank to attempt to use system temp directory. Otherwise enter the absolute path of a writeable directory.')
            		->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::CACHE_LIFETIME_SECONDS)
                    ->setDefaultValue(3600)
            		->setLabel('Cache expiration time (seconds)')
                    ->setDescription('Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).')
                    ->setPositiveInteger()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::DATEFORMAT)
                    ->setDefaultValue('M j, Y')
                    ->setLabel('Date format')
                    ->setDescription('Set the textual formatting of date information for videos. See <a href="http://us.php.net/date">date</a> for examples.')
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::DEBUG_ON)
                    ->setDefaultValue(true)
                    ->setLabel('Enable debugging')
                    ->setDescription('If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL)
                    ->setLabel('Disable <a href="http://php.net/manual/en/function.curl-exec.php">cURL</a> HTTP transport')
                    ->setDefaultValue(false)
                    ->setDescription('Do not attempt to use cURL to fetch remote feeds. Leave enabled unless you know what you are doing.')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP)
                    ->setLabel('Disable <a href="http://php.net/http_request">HTTP extension</a> transport')
                    ->setDefaultValue(false)
                    ->setDescription('Do not attempt to use the PHP HTTP extension to fetch remote feeds. Leave enabled unless you know what you are doing.')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN)
                    ->setLabel('Disable <a href="http://php.net/manual/en/function.fopen.php">fopen</a> HTTP transport')
                    ->setDefaultValue(false)
                    ->setDescription('Do not attempt to use fopen to fetch remote feeds. Leave enabled unless you know what you are doing.')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN)
                    ->setLabel('Disable <a href="http://php.net/fsockopen">fsockopen</a> HTTP transport')
                    ->setDefaultValue(false)
                    ->setDescription('Do not attempt to use fsockopen to fetch remote feeds. Leave enabled unless you know what you are doing.')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS)
                    ->setLabel('Disable <a href="http://php.net/manual/en/intro.stream.php">PHP streams</a> HTTP transport')
                    ->setDefaultValue(false)
                    ->setDescription('Do not attempt to use PHP streams to fetch remote feeds. Leave enabled unless you know what you are doing.')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::GALLERY_ID)
                    ->setValidValueRegex('/\w+/')
                    ->setShouldPersist(false)
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::KEYWORD)
                    ->setLabel('Shortcode keyword')
                    ->setDefaultValue('tubepress')
                    ->setDescription('The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.')
                    ->setWordCharsOnly()
                    ->setCanBeSetViaShortcode(false)
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Advanced::VIDEO_BLACKLIST)
                    ->setLabel('Videos blacklist')
                    ->setDescription('List of video IDs that should never be displayed')
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::AJAX_PAGINATION)
                    ->setLabel('<a href="http://wikipedia.org/wiki/Ajax_(programming)">Ajax</a>-enabled pagination')
                    ->setDefaultValue(false)
                    ->setProOnly(true)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME)
                    ->setLabel('Play each video')
                    ->setDefaultValue(org_tubepress_api_const_options_values_PlayerValue::NORMAL)
                    ->setValueMap(array(
                        org_tubepress_api_const_options_values_PlayerValue::NORMAL    => 'normally (at the top of your gallery)',
                        org_tubepress_api_const_options_values_PlayerValue::POPUP     => 'in a popup window',
                        org_tubepress_api_const_options_values_PlayerValue::YOUTUBE   => 'from the video\'s original YouTube page',
                        org_tubepress_api_const_options_values_PlayerValue::VIMEO     => 'from the video\'s original Vimeo page',
                        org_tubepress_api_const_options_values_PlayerValue::SHADOWBOX => 'with Shadowbox',
                        org_tubepress_api_const_options_values_PlayerValue::JQMODAL   => 'with jqModal',
                        org_tubepress_api_const_options_values_PlayerValue::TINYBOX   => 'with TinyBox',
                        org_tubepress_api_const_options_values_PlayerValue::FANCYBOX  => 'with FancyBox',
                        org_tubepress_api_const_options_values_PlayerValue::STATICC   => 'statically (page refreshes on each thumbnail click)',
                        org_tubepress_api_const_options_values_PlayerValue::SOLO      => 'in a new window on its own',
                        org_tubepress_api_const_options_values_PlayerValue::DETACHED  => 'in a "detached" location (see the documentation)'
                    ))
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::DESC_LIMIT)
                    ->setLabel('Maximum description length')
                    ->setDefaultValue(80)
                    ->setDescription('Maximum number of characters to display in video descriptions. Set to 0 for no limit.')
                    ->setNonNegativeInteger()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::FLUID_THUMBS)
                    ->setLabel('Use "fluid" thumbnails')
                    ->setDefaultValue(true)
                    ->setDescription('Dynamically set thumbnail spacing based on the width of their container.')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::HQ_THUMBS)
                    ->setLabel('Use high-quality thumbnails')
                    ->setDefaultValue(false)
                    ->setDescription('Note: this option cannot be used with the "randomize thumbnails" feature')
                    ->setProOnly(true)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::ORDER_BY)
                    ->setLabel('Order videos by')
                    ->setDefaultValue(org_tubepress_api_const_options_values_OrderValue::VIEW_COUNT)
                    ->setDescription('Not all sort orders can be applied to all gallery types. See the <a href="http://tubepress.org/documentation">documentation</a> for more info.')
                    ->setValueMap(array(
                        org_tubepress_api_const_options_values_OrderValue::RELEVANCE      => 'relevance',
                        org_tubepress_api_const_options_values_OrderValue::VIEW_COUNT     => 'view count',
                        org_tubepress_api_const_options_values_OrderValue::RATING         => 'rating',
                        org_tubepress_api_const_options_values_OrderValue::DATE_PUBLISHED => 'date published',
                        org_tubepress_api_const_options_values_OrderValue::RANDOM         => 'randomly',
                        org_tubepress_api_const_options_values_OrderValue::POSITION       => 'position in a playlist',
                        org_tubepress_api_const_options_values_OrderValue::COMMENT_COUNT  => 'comment count',
                        org_tubepress_api_const_options_values_OrderValue::DURATION       => 'length',
                        org_tubepress_api_const_options_values_OrderValue::TITLE          => 'title',
                        org_tubepress_api_const_options_values_OrderValue::NEWEST         => 'newest',
                        org_tubepress_api_const_options_values_OrderValue::OLDEST         => 'oldest',
                    ))
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::PAGINATE_ABOVE)
                    ->setLabel('Show pagination above thumbnails')
                    ->setDefaultValue(true)
                    ->setDescription('Only applies to galleries that span multiple pages')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::PAGINATE_BELOW)
                    ->setLabel('Show pagination below thumbnails')
                    ->setDefaultValue(true)
                    ->setDescription('Only applies to galleries that span multiple pages')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::RANDOM_THUMBS)
                    ->setLabel('Randomize thumbnails')
                    ->setDefaultValue(true)
                    ->setDescription('Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature')
                    ->setBoolean()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::RELATIVE_DATES)
                    ->setLabel('Use relative dates')
                    ->setDefaultValue(false)
                    ->setDescription('e.g. "yesterday" instead of "November 3, 1980"')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE)
                    ->setLabel('Videos per Page')
                    ->setDefaultValue(20)
                    ->setDescription('Default is 20. Maximum is 50')
                    ->setPositiveInteger()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::THEME)
                    ->setLabel('Theme')
                    ->setDescription('The TubePress theme to use for this gallery. Your themes can be found at <tt>%s</tt>, and default themes can be found at <tt>%s</tt>.')
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::THUMB_HEIGHT)
                    ->setLabel('Height (px) of thumbs')
                    ->setDefaultValue(90)
                    ->setDescription('Default is 90')
                    ->setPositiveInteger()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Display::THUMB_WIDTH)
                    ->setLabel('Width (px) of thumbs')
                    ->setDefaultValue(120)
                    ->setDescription('Default is 120')
                    ->setPositiveInteger()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::AUTOPLAY)
                    ->setLabel('Auto-play all videos')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT)
                    ->setLabel('Max height (px)')
                    ->setDefaultValue(350)
                    ->setDescription('Default is 350')
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH)
                    ->setLabel('Max width (px)')
                    ->setDefaultValue(425)
                    ->setDescription('Default is 425')
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::FULLSCREEN)
                    ->setLabel('Allow fullscreen playback')
                    ->setDefaultValue(true)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY)
                    ->setLabel('Show videos in high definition when available')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::LAZYPLAY)
                    ->setLabel('"Lazy" play videos')
                    ->setDefaultValue(true)
                    ->setDescription('Auto-play each video after thumbnail click')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::LOOP)
                    ->setLabel('Loop')
                    ->setDefaultValue(false)
                    ->setDescription('Continue playing the video until the user stops it')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR)
                    ->setLabel('Main color')
                    ->setDefaultValue('999999')
                    ->setDescription('Default is 999999')
            		->setValidValueRegex('/^([0-9a-f]{1,2}){3}$/i')
            		->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT)
                    ->setLabel('Highlight color')
                    ->setDefaultValue('FFFFFF')
                    ->setDescription('Default is FFFFFF')
            		->setValidValueRegex('/^([0-9a-f]{1,2}){3}$/i')
            		->setYouTubeOnly()
            		->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL)
                    ->setLabel('Implementation')
                    ->setDefaultValue(org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED)
                    ->setDescription('The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc)')
                    ->setYouTubeOnly()
                    ->setValueMap(array(
                        org_tubepress_api_const_options_values_PlayerImplementationValue::EMBEDPLUS      => 'EmbedPlus',
                        org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL       => 'JW FLV Media Player (by Longtail Video)',
                        org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED => 'Provider default',
                    ))
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::SHOW_INFO)
                    ->setLabel('Show title and rating before video starts')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Embedded::SHOW_RELATED)
                    ->setLabel('Show related videos')
                    ->setDefaultValue(true)
                    ->setDescription('Toggles the display of related videos after a video finishes')
                    ->setBoolean()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Feed::CACHE_ENABLED)
                    ->setLabel('Enable request cache')
                    ->setDefaultValue(false)
                    ->setDescription('Store network responses locally for 1 hour. Each response is on the order of a few hundred KB, so leaving the cache enabled will significantly reduce load times for your galleries at the slight expense of freshness.')
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Feed::DEV_KEY)
                    ->setLabel('YouTube API Developer Key')
                    ->setDefaultValue('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg')
                    ->setDescription('YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="http://code.google.com/apis/youtube/dashboard/">here</a>. Don\'t change this unless you know what you\'re doing.')
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY)
                    ->setLabel('Only retrieve embeddable videos')
                    ->setDefaultValue(true)
                    ->setDescription('Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.')
                    ->setBoolean()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Feed::FILTER)
                    ->setLabel('Filter "racy" content')
                    ->setDefaultValue(org_tubepress_api_const_options_values_SafeSearchValue::MODERATE)
                    ->setYouTubeOnly()
                    ->setValueMap(array(
                        org_tubepress_api_const_options_values_SafeSearchValue::NONE     => 'none',
                        org_tubepress_api_const_options_values_SafeSearchValue::MODERATE => 'moderate',
                        org_tubepress_api_const_options_values_SafeSearchValue::STRICT   => 'strict',
                    ))
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP)
                    ->setLabel('Maximum total videos to retrieve')
                    ->setDefaultValue(300)
                    ->setDescription('This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.')
                    ->setNonNegativeInteger()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER)
                    ->setLabel('Restrict search results to videos from this user')
                    ->setDescription('Only applies to search-based galleries')
                    ->setValidValueRegex('/\w+/')
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Feed::VIMEO_KEY)
                    ->setLabel('Vimeo API "Consumer Key"')
                    ->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.')
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Feed::VIMEO_SECRET)
                    ->setLabel('Vimeo API "Consumer Secret"')
                    ->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.')
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::AUTHOR)
                    ->setLabel('Author')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::CATEGORY)
                    ->setLabel('Category')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::DESCRIPTION)
                    ->setLabel('Description')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::ID)
                    ->setLabel('ID')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::LENGTH)
                    ->setLabel('Runtime')
                    ->setDefaultValue(true)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::LIKES)
                    ->setLabel('Likes')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::RATING)
                    ->setLabel('Rating')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::RATINGS)
                    ->setLabel('Ratings')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::TAGS)
                    ->setLabel('Keywords')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::TITLE)
                    ->setLabel('Title')
                    ->setDefaultValue(true)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::UPLOADED)
                    ->setLabel('Posted')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::URL)
                    ->setLabel('URL')
                    ->setDefaultValue(false)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Meta::VIEWS)
                    ->setLabel('Views')
                    ->setDefaultValue(true)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::FAVORITES_VALUE)
                    ->setDefaultValue('mrdeathgod')
                    ->setWordCharsOnly()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::MODE)
                    ->setDefaultValue(org_tubepress_api_const_options_values_ModeValue::FEATURED)
                    ->setValueMap(array(
                        org_tubepress_api_const_options_values_ModeValue::FAVORITES        => 'This YouTube user\'s "favorites"',
                        org_tubepress_api_const_options_values_ModeValue::FEATURED         => 'The latest "featured" videos on YouTube\'s homepage',
                        org_tubepress_api_const_options_values_ModeValue::MOST_DISCUSSED   => 'Most-discussed videos',
                        org_tubepress_api_const_options_values_ModeValue::MOST_RECENT      => 'Most-recently added videos',
                        org_tubepress_api_const_options_values_ModeValue::MOST_RESPONDED   => 'Most-responded to videos',
                        org_tubepress_api_const_options_values_ModeValue::PLAYLIST         => 'This playlist',
                        org_tubepress_api_const_options_values_ModeValue::POPULAR          => 'Most-viewed videos from',
                        org_tubepress_api_const_options_values_ModeValue::TAG              => 'YouTube search for...',
                        org_tubepress_api_const_options_values_ModeValue::TOP_FAVORITES    => 'Videos most frequently flagged as favorites from...',
                        org_tubepress_api_const_options_values_ModeValue::TOP_RATED        => 'Top rated videos from...',
                        org_tubepress_api_const_options_values_ModeValue::USER             => 'Videos from this YouTube user',
                        org_tubepress_api_const_options_values_ModeValue::VIMEO_ALBUM      => 'Videos from this Vimeo album',
                        org_tubepress_api_const_options_values_ModeValue::VIMEO_APPEARS_IN => 'Videos this Vimeo user appears in',
                        org_tubepress_api_const_options_values_ModeValue::VIMEO_CHANNEL    => 'Videos in this Vimeo channel',
                        org_tubepress_api_const_options_values_ModeValue::VIMEO_CREDITED   => 'Videos credited to this Vimeo user (either appears in or uploaded by)',
                        org_tubepress_api_const_options_values_ModeValue::VIMEO_GROUP      => 'Videos from this Vimeo group',
                        org_tubepress_api_const_options_values_ModeValue::VIMEO_LIKES      => 'Videos this Vimeo user likes',
                        org_tubepress_api_const_options_values_ModeValue::VIMEO_SEARCH     => 'Vimeo search for...',
                        org_tubepress_api_const_options_values_ModeValue::VIMEO_UPLOADEDBY => 'Videos uploaded by this Vimeo user'
                    ))
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::MOST_VIEWED_VALUE)
                    ->setDefaultValue(org_tubepress_api_const_options_values_TimeFrameValue::TODAY)
                    ->setTime()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::OUTPUT)
                    ->setShouldPersist(false)
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE)
                    ->setDefaultValue('D2B04665B213AE35')
                    ->setDescription('Limited to 200 videos per playlist. Will usually look something like this: D2B04665B213AE35. Copy the playlist id from the end of the URL in your browser\'s address bar (while looking at a YouTube playlist). It comes right after the "p=". For instance: http://youtube.com/my_playlists?p=D2B04665B213AE35')
                    ->setWordCharsOnly()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::SEARCH_PROVIDER)
                    ->setChooseFrom(array(
                        org_tubepress_api_provider_Provider::YOUTUBE,
                        org_tubepress_api_provider_Provider::VIMEO,
                    ))
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_DOM_ID)
                    ->setProOnly(true)
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY)
                    ->setBoolean()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_URL)
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::TAG_VALUE)
                    ->setDefaultValue('pittsburgh steelers')
                    ->setDescription('YouTube limits this mode to 1,000 results')
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::TOP_RATED_VALUE)
                    ->setDefaultValue(org_tubepress_api_const_options_values_TimeFrameValue::TODAY)
                    ->setTime()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::TOP_FAVORITES_VALUE)
                    ->setDefaultValue(org_tubepress_api_const_options_values_TimeFrameValue::TODAY)
                    ->setTime()
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::USER_VALUE)
                    ->setDefaultValue('3hough')
                    ->setYouTubeOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::VIMEO_UPLOADEDBY_VALUE)
                    ->setDefaultValue('mattkaar')
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::VIMEO_LIKES_VALUE)
                    ->setDefaultValue('coiffier')
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::VIMEO_APPEARS_IN_VALUE)
                    ->setDefaultValue('royksopp')
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE)
                    ->setDefaultValue('cats playing piano')
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::VIMEO_CREDITED_VALUE)
                    ->setDefaultValue('patricklawler')
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::VIMEO_CHANNEL_VALUE)
                    ->setDefaultValue('splitscreenstuff')
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::VIMEO_GROUP_VALUE)
                    ->setDefaultValue('hdxs')
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Output::VIMEO_ALBUM_VALUE)
                    ->setDefaultValue('140484')
                    ->setVimeoOnly()
                    ->build(),

            $builder->setName(org_tubepress_api_const_options_names_Widget::TITLE)
					->setDefaultValue('TubePress')
					->build(),

            $builder->setName(org_tubepress_api_const_options_names_Widget::TAGSTRING)
            		->setDefaultValue('[tubepress thumbHeight=\'105\' thumbWidth=\'135\']')
            		->build()
        );
    }
}

/**
 * Internal class to assist in the construction of OptionDescriptors. DO NOT use this
 * class outside of DefaultOptionDescriptorReference.
 */
class org_tubepress_impl_options_DefaultOptionsDescriptorReferenceBuilder
{
    private $_aliases;
    private $_defaultValue;
    private $_description;
    private $_excludedProviders;
    private $_label;
    private $_name;
    private $_proOnly;
    private $_shortcodeSettable;
    private $_shouldPersist;
    private $_validValueRegex;
    private $_valueMap;

    private static $_valueMapTime = array(

        org_tubepress_api_const_options_values_TimeFrameValue::ALL_TIME   => 'all time',
        org_tubepress_api_const_options_values_TimeFrameValue::THIS_MONTH => 'this month',
        org_tubepress_api_const_options_values_TimeFrameValue::THIS_WEEK  => 'this week',
        org_tubepress_api_const_options_values_TimeFrameValue::TODAY      => 'today',
    );

    private static $_regexBoolean            = '/true|false/i';
    private static $_regexPositiveInteger    = '/[1-9][0-9]{0,6}/';
    private static $_regexNonNegativeInteger = '/0|[1-9][0-9]{0,6}/';
    private static $_regexWordChars          = '/\w+/';

    public function reset()
    {
        $this->_aliases           = array();
        $this->_defaultValue      = null;
        $this->_description       = null;
        $this->_excludedProviders = array();
        $this->_label             = null;
        $this->_name              = null;
        $this->_proOnly           = false;
        $this->_shortcodeSettable = true;
        $this->_shouldPersist     = true;
        $this->_validValueRegex   = null;
        $this->_valueMap          = array();
    }

    public function setName($name)
    {
        $this->_name = $name;
        return $this;
    }

    public function setLabel($label)
    {
        $this->_label = $label;
        return $this;
    }

    public function setDescription($description)
    {
        $this->_description = $description;
        return $this;
    }

    public function setProOnly($proOnly)
    {
        $this->_proOnly = $proOnly;
        return $this;
    }

    public function setAliases($aliases)
    {
        $this->_aliases = $aliases;
        return $this;
    }

    public function setExcludedProviders($providers)
    {
        $this->_excludedProviders = $providers;
        return $this;
    }

    public function setValidValueRegex($regex)
    {
        $this->_validValueRegex = $regex;
        return $this;
    }

    public function setCanBeSetViaShortcode($canBeSet)
    {
        $this->_shortcodeSettable = $canBetSet;
        return $this;
    }

    public function setShouldPersist($shouldPersist)
    {
        $this->_shouldPersist = $shouldPersist;
        return $this;
    }

    public function setDefaultValue($defaultValue)
    {
        $this->_defaultValue = $defaultValue;
        return $this;
    }

    public function setValueMap($map)
    {
        $this->_valueMap = $map;
        return $this;
    }

    public function setBoolean()
    {
        $this->_validValueRegex = self::$_regexBoolean;
        return $this;
    }

    public function setPositiveInteger()
    {
        $this->_validValueRegex = self::$_regexPositiveInteger;
        return $this;
    }

    public function setNonNegativeInteger()
    {
        $this->_validValueRegex = self::$_regexNonNegativeInteger;
        return $this;
    }

    public function setWordCharsOnly()
    {
        $this->_validValueRegex = self::$_regexWordChars;
        return $this;
    }

    public function setChooseFrom($values)
    {
        $this->_validValueRegex = '/' . implode("|", $values) . '/';
        return $this;
    }

    public function setTime()
    {
        $this->_valueMap = self::$_valueMapTime;
        return $this;
    }

    public function setVimeoOnly()
    {
        $this->_excludedProviders = array(org_tubepress_api_provider_Provider::YOUTUBE);
        return $this;
    }

    public function setYouTubeOnly()
    {
        $this->_excludedProviders = array(org_tubepress_api_provider_Provider::VIMEO);
        return $this;
    }

    public function build()
    {
        return new org_tubepress_api_options_OptionDescriptor(
            $this->_name,
            $this->_label,
            $this->_defaultValue,
            $this->_description,
            $this->_proOnly,
            $this->_aliases,
            $this->_excludedProviders,
            $this->_validValueRegex,
            $this->_shortcodeSettable,
            $this->_shouldPersist,
            $this->_valueMap
        );

        $this->reset();
    }
}