<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com);
 *
 * This file is part of TubePress (http://tubepress.org);
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
    'org_tubepress_api_options_OptionDescriptor',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Feed',
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_names_Widget',
    'org_tubepress_api_const_options_values_ModeValue',
    'org_tubepress_api_const_options_values_PlayerImplementationValue',
    'org_tubepress_api_const_options_values_PlayerValue',
    'org_tubepress_api_const_options_values_OrderValue',
    'org_tubepress_api_const_options_values_SafeSearchValue',
    'org_tubepress_api_const_options_values_TimeFrameValue'
));

/**
 * Holds all the option descriptors for TubePress. This implementation just holds them in memory.
 */
class org_tubepress_impl_options_DefaultOptionDescriptorReference implements org_tubepress_api_options_OptionDescriptorReference
{
    /** Provides fast lookup by name. */
    private $_nameToOptionDescriptorMap = array();

    /** All option descriptors. */
    private $_optionDescriptors = array();

    private static $_regexBoolean            = '/true|false/i';
    private static $_regexPositiveInteger    = '/[1-9][0-9]{0,6}/';
    private static $_regexNonNegativeInteger = '/0|[1-9][0-9]{0,6}/';
    private static $_regexWordChars          = '/\w+/';
    private static $_valueMapTime = array(

        org_tubepress_api_const_options_values_TimeFrameValue::ALL_TIME   => 'all time',
        org_tubepress_api_const_options_values_TimeFrameValue::THIS_MONTH => 'this month',
        org_tubepress_api_const_options_values_TimeFrameValue::THIS_WEEK  => 'this week',
        org_tubepress_api_const_options_values_TimeFrameValue::TODAY      => 'today',
    );
    private static $_providerArrayYouTube = array(org_tubepress_api_provider_Provider::YOUTUBE);
    private static $_providerArrayVimeo = array(org_tubepress_api_provider_Provider::VIMEO);

    public function __construct()
    {
        /* build all the option descriptors. */
        $this->_buildAllOptionDescriptors();
    }

    public function findAll()
    {
        return $this->_optionDescriptors;
    }

    function findOneByName($name)
    {
        return $this->_nameToOptionDescriptorMap[$name];
    }

    public function register(org_tubepress_api_options_OptionDescriptor $descriptor)
    {
        if (array_key_exists($descriptor->getName(), $this->_nameToOptionDescriptorMap)) {

            throw new Exception($descriptor->getName() . ' is already registered as an option descriptor');
        }

        array_push($this->_optionDescriptors, $descriptor);

        $this->_nameToOptionDescriptorMap[$descriptor->getName()] = $descriptor;
    }

    private function _buildAllOptionDescriptors()
    {
        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::CACHE_CLEAN_FACTOR);
        $option->setDefaultValue(20);
        $option->setLabel('Cache cleaning factor');
        $option->setDescription('If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.');
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::CACHE_DIR);
        $option->setLabel('Cache directory');
        $option->setDescription('Leave blank to attempt to use system temp directory. Otherwise enter the absolute path of a writeable directory.');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::CACHE_LIFETIME_SECONDS);
        $option->setDefaultValue(3600);
        $option->setLabel('Cache expiration time (seconds)');
        $option->setDescription('Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).');
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DATEFORMAT);
        $option->setDefaultValue('M j, Y');
        $option->setLabel('Date format');
        $option->setDescription('Set the textual formatting of date information for videos. See <a href="http://us.php.net/date">date</a> for examples.');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $option->setDefaultValue(true);
        $option->setLabel('Enable debugging');
        $option->setDescription('If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL);
        $option->setLabel('Disable <a href="http://php.net/manual/en/function.curl-exec.php">cURL</a> HTTP transport');
        $option->setDefaultValue(false);
        $option->setDescription('Do not attempt to use cURL to fetch remote feeds. Leave enabled unless you know what you are doing.');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP);
        $option->setLabel('Disable <a href="http://php.net/http_request">HTTP extension</a> transport');
        $option->setDefaultValue(false);
        $option->setDescription('Do not attempt to use the PHP HTTP extension to fetch remote feeds. Leave enabled unless you know what you are doing.');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN);
        $option->setLabel('Disable <a href="http://php.net/manual/en/function.fopen.php">fopen</a> HTTP transport');
        $option->setDefaultValue(false);
        $option->setDescription('Do not attempt to use fopen to fetch remote feeds. Leave enabled unless you know what you are doing.');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN);
        $option->setLabel('Disable <a href="http://php.net/fsockopen">fsockopen</a> HTTP transport');
        $option->setDefaultValue(false);
        $option->setDescription('Do not attempt to use fsockopen to fetch remote feeds. Leave enabled unless you know what you are doing.');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS);
        $option->setLabel('Disable <a href="http://php.net/manual/en/intro.stream.php">PHP streams</a> HTTP transport');
        $option->setDefaultValue(false);
        $option->setDescription('Do not attempt to use PHP streams to fetch remote feeds. Leave enabled unless you know what you are doing.');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::GALLERY_ID);
        $option->setValidValueRegex('/\w+/');
        $option->setDoNotPersist();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::KEYWORD);
        $option->setLabel('Shortcode keyword');
        $option->setDefaultValue('tubepress');
        $option->setDescription('The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.');
        $option->setValidValueRegex(self::$_regexWordChars);
        $option->setCannotBeSetViaShortcode();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::VIDEO_BLACKLIST);
        $option->setLabel('Videos blacklist');
        $option->setDescription('List of video IDs that should never be displayed');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::AJAX_PAGINATION);
        $option->setLabel('<a href="http://wikipedia.org/wiki/Ajax_(programming)">Ajax</a>-enabled pagination');
        $option->setDefaultValue(false);
        $option->setProOnly();
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME);
        $option->setLabel('Play each video');
        $option->setDefaultValue(org_tubepress_api_const_options_values_PlayerValue::NORMAL);
        $option->setAcceptableValues(array(
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
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::DESC_LIMIT);
        $option->setLabel('Maximum description length');
        $option->setDefaultValue(80);
        $option->setDescription('Maximum number of characters to display in video descriptions. Set to 0 for no limit.');
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::FLUID_THUMBS);
        $option->setLabel('Use "fluid" thumbnails');
        $option->setDefaultValue(true);
        $option->setDescription('Dynamically set thumbnail spacing based on the width of their container.');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::HQ_THUMBS);
        $option->setLabel('Use high-quality thumbnails');
        $option->setDefaultValue(false);
        $option->setDescription('Note: this option cannot be used with the "randomize thumbnails" feature');
        $option->setProOnly();
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::ORDER_BY);
        $option->setLabel('Order videos by');
        $option->setDefaultValue(org_tubepress_api_const_options_values_OrderValue::VIEW_COUNT);
        $option->setDescription('Not all sort orders can be applied to all gallery types. See the <a href="http://tubepress.org/documentation">documentation</a> for more info.');
        $option->setAcceptableValues(array(
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
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::PAGINATE_ABOVE);
        $option->setLabel('Show pagination above thumbnails');
        $option->setDefaultValue(true);
        $option->setDescription('Only applies to galleries that span multiple pages');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::PAGINATE_BELOW);
        $option->setLabel('Show pagination below thumbnails');
        $option->setDefaultValue(true);
        $option->setDescription('Only applies to galleries that span multiple pages');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::RANDOM_THUMBS);
        $option->setLabel('Randomize thumbnails');
        $option->setDefaultValue(true);
        $option->setDescription('Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature');
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::RELATIVE_DATES);
        $option->setLabel('Use relative dates');
        $option->setDefaultValue(false);
        $option->setDescription('e.g. "yesterday" instead of "November 3, 1980"');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE);
        $option->setLabel('Videos per Page');
        $option->setDefaultValue(20);
        $option->setDescription('Default is 20. Maximum is 50');
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::THEME);
        $option->setLabel('Theme');
        $option->setDescription('The TubePress theme to use for this gallery. Your themes can be found at <tt>%s</tt>, and default themes can be found at <tt>%s</tt>.');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::THUMB_HEIGHT);
        $option->setLabel('Height (px) of thumbs');
        $option->setDefaultValue(90);
        $option->setDescription('Default is 90');
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Display::THUMB_WIDTH);
        $option->setLabel('Width (px) of thumbs');
        $option->setDefaultValue(120);
        $option->setDescription('Default is 120');
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $option->setLabel('Auto-play all videos');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);
        $option->setLabel('Max height (px)');
        $option->setDefaultValue(350);
        $option->setDescription('Default is 350');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $option->setLabel('Max width (px)');
        $option->setDefaultValue(425);
        $option->setDescription('Default is 425');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::FULLSCREEN);
        $option->setLabel('Allow fullscreen playback');
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY);
        $option->setLabel('Show videos in high definition when available');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::LAZYPLAY);
        $option->setLabel('"Lazy" play videos');
        $option->setDefaultValue(true);
        $option->setDescription('Auto-play each video after thumbnail click');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::LOOP);
        $option->setLabel('Loop');
        $option->setDefaultValue(false);
        $option->setDescription('Continue playing the video until the user stops it');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR);
        $option->setLabel('Main color');
        $option->setDefaultValue('999999');
        $option->setDescription('Default is 999999');
        $option->setValidValueRegex('/^([0-9a-f]{1,2}){3}$/i');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT);
        $option->setLabel('Highlight color');
        $option->setDefaultValue('FFFFFF');
        $option->setDescription('Default is FFFFFF');
        $option->setValidValueRegex('/^([0-9a-f]{1,2}){3}$/i');
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
        $option->setLabel('Implementation');
        $option->setDefaultValue(org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);
        $option->setDescription('The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc)');
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setAcceptableValues(array(
            org_tubepress_api_const_options_values_PlayerImplementationValue::EMBEDPLUS      => 'EmbedPlus',
            org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL       => 'JW FLV Media Player (by Longtail Video)',
            org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED => 'Provider default',
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $option->setLabel('Show title and rating before video starts');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::SHOW_RELATED);
        $option->setLabel('Show related videos');
        $option->setDefaultValue(true);
        $option->setDescription('Toggles the display of related videos after a video finishes');
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::CACHE_ENABLED);
        $option->setLabel('Enable request cache');
        $option->setDefaultValue(false);
        $option->setDescription('Store network responses locally for 1 hour. Each response is on the order of a few hundred KB, so leaving the cache enabled will significantly reduce load times for your galleries at the slight expense of freshness.');
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::DEV_KEY);
        $option->setLabel('YouTube API Developer Key');
        $option->setDefaultValue('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $option->setDescription('YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="http://code.google.com/apis/youtube/dashboard/">here</a>. Don\'t change this unless you know what you\'re doing.');
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY);
        $option->setLabel('Only retrieve embeddable videos');
        $option->setDefaultValue(true);
        $option->setDescription('Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.');
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::FILTER);
        $option->setLabel('Filter "racy" content');
        $option->setDefaultValue(org_tubepress_api_const_options_values_SafeSearchValue::MODERATE);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setAcceptableValues(array(
            org_tubepress_api_const_options_values_SafeSearchValue::NONE     => 'none',
            org_tubepress_api_const_options_values_SafeSearchValue::MODERATE => 'moderate',
            org_tubepress_api_const_options_values_SafeSearchValue::STRICT   => 'strict',
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $option->setLabel('Maximum total videos to retrieve');
        $option->setDefaultValue(300);
        $option->setDescription('This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.');
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
        $option->setLabel('Restrict search results to videos from this user');
        $option->setDescription('Only applies to search-based galleries');
        $option->setValidValueRegex('/\w+/');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::VIMEO_KEY);
        $option->setLabel('Vimeo API "Consumer Key"');
        $option->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::VIMEO_SECRET);
        $option->setLabel('Vimeo API "Consumer Secret"');
        $option->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::AUTHOR);
        $option->setLabel('Author');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::CATEGORY);
        $option->setLabel('Category');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::DESCRIPTION);
        $option->setLabel('Description');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::ID);
        $option->setLabel('ID');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::LENGTH);
        $option->setLabel('Runtime');
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::LIKES);
        $option->setLabel('Likes');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::RATING);
        $option->setLabel('Rating');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::RATINGS);
        $option->setLabel('Ratings');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::TAGS);
        $option->setLabel('Keywords');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::TITLE);
        $option->setLabel('Title');
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::UPLOADED);
        $option->setLabel('Posted');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::URL);
        $option->setLabel('URL');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::VIEWS);
        $option->setLabel('Views');
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::FAVORITES_VALUE);
        $option->setDefaultValue('mrdeathgod');
        $option->setValidValueRegex(self::$_regexWordChars);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::MODE);
        $option->setDefaultValue(org_tubepress_api_const_options_values_ModeValue::FEATURED);
        $option->setAcceptableValues(array(
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
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::MOST_VIEWED_VALUE);
        $option->setDefaultValue(org_tubepress_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::OUTPUT);
        $option->setDoNotPersist();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE);
        $option->setDefaultValue('D2B04665B213AE35');
        $option->setDescription('Limited to 200 videos per playlist. Will usually look something like this: D2B04665B213AE35. Copy the playlist id from the end of the URL in your browser\'s address bar (while looking at a YouTube playlist). It comes right after the "p=". For instance: http://youtube.com/my_playlists?p=D2B04665B213AE35');
        $option->setValidValueRegex(self::$_regexWordChars);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::SEARCH_PROVIDER);
        $option->setAcceptableValues(array(
            org_tubepress_api_provider_Provider::YOUTUBE,
            org_tubepress_api_provider_Provider::VIMEO,
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_DOM_ID);
        $option->setProOnly();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_URL);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::TAG_VALUE);
        $option->setDefaultValue('pittsburgh steelers');
        $option->setDescription('YouTube limits this mode to 1,000 results');
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::TOP_RATED_VALUE);
        $option->setDefaultValue(org_tubepress_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::TOP_FAVORITES_VALUE);
        $option->setDefaultValue(org_tubepress_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::USER_VALUE);
        $option->setDefaultValue('3hough');
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::VIMEO_UPLOADEDBY_VALUE);
        $option->setDefaultValue('mattkaar');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::VIMEO_LIKES_VALUE);
        $option->setDefaultValue('coiffier');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::VIMEO_APPEARS_IN_VALUE);
        $option->setDefaultValue('royksopp');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE);
        $option->setDefaultValue('cats playing piano');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::VIMEO_CREDITED_VALUE);
        $option->setDefaultValue('patricklawler');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::VIMEO_CHANNEL_VALUE);
        $option->setDefaultValue('splitscreenstuff');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::VIMEO_GROUP_VALUE);
        $option->setDefaultValue('hdxs');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::VIMEO_ALBUM_VALUE);
        $option->setDefaultValue('140484');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Widget::TITLE);
        $option->setDefaultValue('TubePress');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Widget::TAGSTRING);
        $option->setDefaultValue('[tubepress thumbHeight=\'105\' thumbWidth=\'135\']');
        $this->register($option);
    }
}
