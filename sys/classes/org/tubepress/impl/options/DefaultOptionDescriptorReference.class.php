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
	'org_tubepress_api_const_options_names_Cache',
    'org_tubepress_api_const_options_names_Embedded',
    'org_tubepress_api_const_options_names_Feed',
	'org_tubepress_api_const_options_names_GallerySource',
	'org_tubepress_api_const_options_names_InteractiveSearch',
    'org_tubepress_api_const_options_names_Output',
	'org_tubepress_api_const_options_names_Thumbs',
	'org_tubepress_api_const_options_names_Meta',
    'org_tubepress_api_const_options_names_WordPress',
    'org_tubepress_api_const_options_values_GallerySourceValue',
    'org_tubepress_api_const_options_values_PlayerImplementationValue',
    'org_tubepress_api_const_options_values_PlayerLocationValue',
    'org_tubepress_api_const_options_values_OrderByValue',
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
    private static $_regexColor              = '/^([0-9a-f]{1,2}){3}$/i';
    private static $_regexWordChars          = '/\w+/';
    private static $_valueMapTime = array(

        org_tubepress_api_const_options_values_TimeFrameValue::ALL_TIME   => 'all time',        //>(translatable)<
        org_tubepress_api_const_options_values_TimeFrameValue::THIS_MONTH => 'this month',      //>(translatable)<
        org_tubepress_api_const_options_values_TimeFrameValue::THIS_WEEK  => 'this week',       //>(translatable)<
        org_tubepress_api_const_options_values_TimeFrameValue::TODAY      => 'today',           //>(translatable)<
    );
    private static $_providerArrayYouTube = array(org_tubepress_api_provider_Provider::YOUTUBE);
    private static $_providerArrayVimeo = array(org_tubepress_api_provider_Provider::VIMEO);

    /**
     * Constructor.
     */
    public function __construct()
    {
        /* build all the option descriptors. */
        $this->_buildAllOptionDescriptors();
    }

    /**
     * Returns all of the option descriptors.
     *
     * @return array All of the registered option descriptors.
     */
    public function findAll()
    {
        return $this->_optionDescriptors;
    }

    /**
     * Finds a single option descriptor by name, or null if no such option.
     *
     * @param string $name The option descriptor to look up.
     *
     * @return org_tubepress_api_options_OptionDescriptor The option descriptor with the
     *                                                    given name, or null if not found.
     */
    public function findOneByName($name)
    {
        return $this->_nameToOptionDescriptorMap[$name];
    }

    /**
     * Register a new option descriptor.
     *
     * @param org_tubepress_api_options_OptionDescriptor $descriptor The descriptor to register.
     *
     * @throws Exception If this option descriptor already exists.
     */
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
        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $option->setDefaultValue(true);
        $option->setLabel('Enable debugging');                                                                                                                                                                                                                                                         //>(translatable)<
        $option->setDescription('If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.');  //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL);
        $option->setDefaultValue(false);
        $option->setLabel('Disable <a href="http://php.net/manual/en/function.curl-exec.php">cURL</a> HTTP transport');                    //>(translatable)<
        $option->setDescription('Do not attempt to use cURL to fetch remote feeds. Leave enabled unless you know what you are doing.');    //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP);
        $option->setDefaultValue(false);
        $option->setLabel('Disable <a href="http://php.net/http_request">HTTP extension</a> transport');                                                  //>(translatable)<
        $option->setDescription('Do not attempt to use the PHP HTTP extension to fetch remote feeds. Leave enabled unless you know what you are doing.'); //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN);
        $option->setDefaultValue(false);
        $option->setLabel('Disable <a href="http://php.net/manual/en/function.fopen.php">fopen</a> HTTP transport');                     //>(translatable)<
        $option->setDescription('Do not attempt to use fopen to fetch remote feeds. Leave enabled unless you know what you are doing.'); //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN);
        $option->setDefaultValue(false);
        $option->setLabel('Disable <a href="http://php.net/fsockopen">fsockopen</a> HTTP transport');                                        //>(translatable)<
        $option->setDescription('Do not attempt to use fsockopen to fetch remote feeds. Leave enabled unless you know what you are doing.'); //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS);
        $option->setDefaultValue(false);
        $option->setLabel('Disable <a href="http://php.net/manual/en/intro.stream.php">PHP streams</a> HTTP transport');                        //>(translatable)<
        $option->setDescription('Do not attempt to use PHP streams to fetch remote feeds. Leave enabled unless you know what you are doing.');  //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::GALLERY_ID);
        $option->setValidValueRegex('/\w+/');
        $option->setDoNotPersist();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Advanced::KEYWORD);
        $option->setDefaultValue('tubepress');
        $option->setLabel('Shortcode keyword');                                                                                             //>(translatable)<
        $option->setDescription('The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $option->setCannotBeSetViaShortcode();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR);
        $option->setDefaultValue(20);
        $option->setLabel('Cache cleaning factor');                                                                                             //>(translatable)<
        $option->setDescription('If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Cache::CACHE_DIR);
        $option->setLabel('Cache directory');                                                                                                                //>(translatable)<
        $option->setDescription('Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.'); //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Cache::CACHE_ENABLED);
        $option->setDefaultValue(false);
        $option->setLabel('Enable request cache');                                                                                                                    //>(translatable)<
        $option->setDescription('Store network responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.'); //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS);
        $option->setDefaultValue(3600);
        $option->setLabel('Cache expiration time (seconds)');                                                                                   //>(translatable)<
        $option->setDescription('Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).');   //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $option->setLabel('Play each video');                                                                                                 //>(translatable)<
        $option->setDefaultValue(org_tubepress_api_const_options_values_PlayerLocationValue::NORMAL);
        $option->setAcceptableValues(array(
            org_tubepress_api_const_options_values_PlayerLocationValue::NORMAL    => 'normally (at the top of your gallery)',                 //>(translatable)<
            org_tubepress_api_const_options_values_PlayerLocationValue::POPUP     => 'in a popup window',                                     //>(translatable)<
            org_tubepress_api_const_options_values_PlayerLocationValue::YOUTUBE   => 'from the video\'s original YouTube page',               //>(translatable)<
            org_tubepress_api_const_options_values_PlayerLocationValue::VIMEO     => 'from the video\'s original Vimeo page',                 //>(translatable)<
            org_tubepress_api_const_options_values_PlayerLocationValue::SHADOWBOX => 'with Shadowbox',                                        //>(translatable)<
            org_tubepress_api_const_options_values_PlayerLocationValue::JQMODAL   => 'with jqModal',                                          //>(translatable)<
            org_tubepress_api_const_options_values_PlayerLocationValue::TINYBOX   => 'with TinyBox',                                          //>(translatable)<
            org_tubepress_api_const_options_values_PlayerLocationValue::FANCYBOX  => 'with FancyBox',                                         //>(translatable)<
            org_tubepress_api_const_options_values_PlayerLocationValue::STATICC   => 'statically (page refreshes on each thumbnail click)',   //>(translatable)<
            org_tubepress_api_const_options_values_PlayerLocationValue::SOLO      => 'in a new window on its own',                            //>(translatable)<
            org_tubepress_api_const_options_values_PlayerLocationValue::DETACHED  => 'in a "detached" location (see the documentation)'		  //>(translatable)<
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $option->setLabel('Auto-play all videos');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);
        $option->setDefaultValue(350);
        $option->setLabel('Max height (px)');      //>(translatable)<
        $option->setDescription('Default is 350'); //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $option->setDefaultValue(425);
        $option->setLabel('Max width (px)');       //>(translatable)<
        $option->setDescription('Default is 425'); //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::FULLSCREEN);
        $option->setLabel('Allow fullscreen playback');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY);
        $option->setLabel('Allow high definition playback');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::LAZYPLAY);
        $option->setDefaultValue(true);
        $option->setLabel('"Lazy" play videos');                               //>(translatable)<
        $option->setDescription('Auto-play each video after thumbnail click'); //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::LOOP);
        $option->setDefaultValue(false);
        $option->setLabel('Loop');                                                     //>(translatable)<
        $option->setDescription('Continue playing the video until the user stops it'); //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR);
        $option->setDefaultValue('999999');
        $option->setLabel('Main color');              //>(translatable)<
        $option->setDescription('Default is 999999'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT);
        $option->setDefaultValue('FFFFFF');
        $option->setLabel('Highlight color');         //>(translatable)<
        $option->setDescription('Default is FFFFFF'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexColor);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
        $option->setDefaultValue(org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);
        $option->setLabel('Implementation');                                                                                  //>(translatable)<
        $option->setDescription('The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc)'); //>(translatable)<
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setAcceptableValues(array(
            org_tubepress_api_const_options_values_PlayerImplementationValue::EMBEDPLUS      => 'EmbedPlus',
            org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL       => 'JW FLV Media Player (by Longtail Video)',  //>(translatable)<
            org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED => 'Provider default',                         //>(translatable)<
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $option->setLabel('Show title and rating before video starts');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Embedded::SHOW_RELATED);
        $option->setDefaultValue(true);
        $option->setLabel('Show related videos');                                                //>(translatable)<
        $option->setDescription('Toggles the display of related videos after a video finishes'); //>(translatable)<
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST);
        $option->setLabel('Videos blacklist');                                        //>(translatable)<
        $option->setDescription('List of video IDs that should never be displayed');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::ORDER_BY);
        $option->setDefaultValue(org_tubepress_api_const_options_values_OrderByValue::VIEW_COUNT);
        $option->setLabel('Order videos by');                                                                                                                                      //>(translatable)<
        $option->setDescription('Not all sort orders can be applied to all gallery types. See the <a href="http://tubepress.org/documentation">documentation</a> for more info.'); //>(translatable)<
        $option->setAcceptableValues(array(
            org_tubepress_api_const_options_values_OrderByValue::RELEVANCE      => 'relevance',                //>(translatable)<
            org_tubepress_api_const_options_values_OrderByValue::VIEW_COUNT     => 'view count',               //>(translatable)<
            org_tubepress_api_const_options_values_OrderByValue::RATING         => 'rating',                   //>(translatable)<
            org_tubepress_api_const_options_values_OrderByValue::DATE_PUBLISHED => 'date published',           //>(translatable)<
            org_tubepress_api_const_options_values_OrderByValue::RANDOM         => 'randomly',                 //>(translatable)<
            org_tubepress_api_const_options_values_OrderByValue::POSITION       => 'position in a playlist',   //>(translatable)<
            org_tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT  => 'comment count',            //>(translatable)<
            org_tubepress_api_const_options_values_OrderByValue::DURATION       => 'length',                   //>(translatable)<
            org_tubepress_api_const_options_values_OrderByValue::TITLE          => 'title',                    //>(translatable)<
            org_tubepress_api_const_options_values_OrderByValue::NEWEST         => 'newest',                   //>(translatable)<
            org_tubepress_api_const_options_values_OrderByValue::OLDEST         => 'oldest',                   //>(translatable)<
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::DEV_KEY);
        $option->setDefaultValue('AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg');
        $option->setLabel('YouTube API Developer Key');                                                                                                                                                                                                                                                                                   //>(translatable)<
        $option->setDescription('YouTube will use this developer key for logging and debugging purposes if you experience a service problem on their end. You can register a new client ID and developer key <a href="http://code.google.com/apis/youtube/dashboard/">here</a>. Don\'t change this unless you know what you\'re doing.'); //>(translatable)<
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY);
        $option->setDefaultValue(true);
        $option->setLabel('Only retrieve embeddable videos');                                                                                //>(translatable)<
        $option->setDescription('Some videos have embedding disabled. Checking this option will exclude these videos from your galleries.'); //>(translatable)<
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::FILTER);
        $option->setLabel('Filter "racy" content');                                                    //>(translatable)<
        $option->setDescription('Don\'t show videos that may not be suitable for minors.');            //>(translatable)<
        $option->setDefaultValue(org_tubepress_api_const_options_values_SafeSearchValue::GALLERY_SOURCERATE);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setAcceptableValues(array(
            org_tubepress_api_const_options_values_SafeSearchValue::NONE     => 'none',     //>(translatable)<
            org_tubepress_api_const_options_values_SafeSearchValue::GALLERY_SOURCERATE => 'moderate', //>(translatable)<
            org_tubepress_api_const_options_values_SafeSearchValue::STRICT   => 'strict',   //>(translatable)<
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $option->setDefaultValue(300);
        $option->setLabel('Maximum total videos to retrieve');                                                                   //>(translatable)<
        $option->setDescription('This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
        $option->setLabel('Restrict search results to videos from this user');  //>(translatable)<
        $option->setDescription('Only applies to search-based galleries');      //>(translatable)<
        $option->setValidValueRegex('/\w+/');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::VIMEO_KEY);
        $option->setLabel('Vimeo API "Consumer Key"');                                                                                        //>(translatable)<
        $option->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.'); //>(translatable)<
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Feed::VIMEO_SECRET);
        $option->setLabel('Vimeo API "Consumer Secret"');                                                                                     //>(translatable)<
        $option->setDescription('<a href="http://vimeo.com/api/applications/new">Click here</a> to register for a consumer key and secret.'); //>(translatable)<
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TAG_VALUE);
        $option->setDefaultValue('pittsburgh steelers');
        $option->setDescription('YouTube limits this mode to 1,000 results');  //>(translatable)<
        $option->setLabel('YouTube search for...');                            //>(translatable)<
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_RATED_VALUE);
        $option->setDefaultValue(org_tubepress_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Top rated videos from...');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_TOP_FAVORITES_VALUE);
        $option->setDefaultValue(org_tubepress_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Videos most frequently flagged as favorites from...');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_USER_VALUE);
        $option->setDefaultValue('3hough');
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Videos from this YouTube user');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::VIMEO_UPLOADEDBY_VALUE);
        $option->setDefaultValue('mattkaar');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $option->setLabel('Videos uploaded by this Vimeo user');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::VIMEO_LIKES_VALUE);
        $option->setDefaultValue('coiffier');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $option->setLabel('Videos this Vimeo user likes');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::VIMEO_APPEARS_IN_VALUE);
        $option->setDefaultValue('royksopp');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $option->setLabel('Videos this Vimeo user appears in');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::VIMEO_SEARCH_VALUE);
        $option->setDefaultValue('cats playing piano');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $option->setLabel('Vimeo search for...');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::VIMEO_CREDITED_VALUE);
        $option->setDefaultValue('patricklawler');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $option->setLabel('Videos credited to this Vimeo user (either appears in or uploaded by)');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::VIMEO_CHANNEL_VALUE);
        $option->setDefaultValue('splitscreenstuff');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $option->setLabel('Videos in this Vimeo channel');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::VIMEO_GROUP_VALUE);
        $option->setDefaultValue('hdxs');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $option->setLabel('Videos from this Vimeo group');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::VIMEO_ALBUM_VALUE);
        $option->setDefaultValue('140484');
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $option->setLabel('Videos from this Vimeo album');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_FAVORITES_VALUE);
        $option->setDefaultValue('mrdeathgod');
        $option->setValidValueRegex(self::$_regexWordChars);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('This YouTube user\'s "favorites"');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_VIEWED_VALUE);
        $option->setDefaultValue(org_tubepress_api_const_options_values_TimeFrameValue::TODAY);
        $option->setAcceptableValues(self::$_valueMapTime);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Most-viewed videos from');  //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_PLAYLIST_VALUE);
        $option->setDefaultValue('D2B04665B213AE35');
        $option->setDescription('Limited to 200 videos per playlist. Will usually look something like this: D2B04665B213AE35. Copy the playlist id from the end of the URL in your browser\'s address bar (while looking at a YouTube playlist). It comes right after the "p=". For instance: http://youtube.com/my_playlists?p=D2B04665B213AE35');  //>(translatable)<
        $option->setLabel('This playlist');                                                                                                                                                                                                                                                                                                          //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_FEATURED);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('The latest "featured" videos on YouTube\'s homepage');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $this->register($option);
        
        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_DISCUSSED_VALUE);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Most-discussed videos');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $this->register($option);
        
        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_RECENT_VALUE);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Most-recently added videos');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $this->register($option);
        
        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_GallerySource::YOUTUBE_MOST_RESPONDED_VALUE);
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $option->setLabel('Most-responded to videos');    //>(translatable)<
        $option->setAcceptableValues(self::$_valueMapTime);
        $this->register($option);
        
        
        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER);
        $option->setAcceptableValues(array(
            org_tubepress_api_provider_Provider::YOUTUBE,
            org_tubepress_api_provider_Provider::VIMEO,
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_DOM_ID);
        $option->setProOnly();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::DATEFORMAT);
        $option->setDefaultValue('M j, Y');
        $option->setLabel('Date format');                                                                                                                    //>(translatable)<
        $option->setDescription('Set the textual formatting of date information for videos. See <a href="http://us.php.net/date">date</a> for examples.');   //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::DESC_LIMIT);
        $option->setDefaultValue(80);
        $option->setLabel('Maximum description length');                                                                  //>(translatable)<
        $option->setDescription('Maximum number of characters to display in video descriptions. Set to 0 for no limit.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::RELATIVE_DATES);
        $option->setDefaultValue(false);
        $option->setLabel('Use relative dates');                                    //>(translatable)<
        $option->setDescription('e.g. "yesterday" instead of "November 3, 1980"');  //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::AUTHOR);
        $option->setLabel('Author');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::CATEGORY);
        $option->setLabel('Category');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::DESCRIPTION);
        $option->setLabel('Description');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::ID);
        $option->setLabel('ID');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::LENGTH);
        $option->setLabel('Runtime');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::LIKES);
        $option->setLabel('Likes');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayYouTube);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::RATING);
        $option->setLabel('Rating');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::RATINGS);
        $option->setLabel('Ratings');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::KEYWORDS);
        $option->setLabel('Keywords');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::TITLE);
        $option->setLabel('Title');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::UPLOADED);
        $option->setLabel('Posted');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::URL);
        $option->setLabel('URL');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Meta::VIEWS);
        $option->setLabel('Views');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE);
        $option->setDefaultValue(org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED);
        $option->setAcceptableValues(array(
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_VIEWED,
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
            org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
            org_tubepress_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY,
        ));
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Output::OUTPUT);
        $option->setDoNotPersist();
        $this->register($option);


        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION);
        $option->setLabel('<a href="http://wikipedia.org/wiki/Ajax_(programming)">Ajax</a>-enabled pagination'); //>(translatable)<
        $option->setDefaultValue(false);
        $option->setProOnly();
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Thumbs::FLUID_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Use "fluid" thumbnails');                                                         //>(translatable)<
        $option->setDescription('Dynamically set thumbnail spacing based on the width of their container.'); //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Thumbs::HQ_THUMBS);
        $option->setDefaultValue(false);
        $option->setLabel('Use high-quality thumbnails');                                                    //>(translatable)<
        $option->setDescription('Note: this option cannot be used with the "randomize thumbnails" feature'); //>(translatable)<
        $option->setProOnly();
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination above thumbnails');                         //>(translatable)<
        $option->setDescription('Only applies to galleries that span multiple pages'); //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination below thumbnails');                         //>(translatable)<
        $option->setDescription('Only applies to galleries that span multiple pages'); //>(translatable)<
        $option->setBoolean();
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Randomize thumbnail images');                                                                                                                                                                                                                                              //>(translatable)<
        $option->setDescription('Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature'); //>(translatable)<
        $option->setBoolean();
        $option->setExcludedProviders(self::$_providerArrayVimeo);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);
        $option->setDefaultValue(20);
        $option->setLabel('Thumbnails per page');                    //>(translatable)<
        $option->setDescription('Default is 20. Maximum is 50');     //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Thumbs::THEME);
        $option->setLabel('Theme');                                                                                                                                       //>(translatable)<
        $option->setDescription('The TubePress theme to use for this gallery. Your themes can be found at <tt>%s</tt>, and default themes can be found at <tt>%s</tt>.'); //>(translatable)<
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT);
        $option->setDefaultValue(90);
        $option->setLabel('Height (px) of thumbs'); //>(translatable)<
        $option->setDescription('Default is 90');   //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_Thumbs::THUMB_WIDTH);
        $option->setDefaultValue(120);
        $option->setLabel('Width (px) of thumbs');  //>(translatable)<
        $option->setDescription('Default is 120');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_WordPress::WIDGET_TITLE);
        $option->setDefaultValue('TubePress');
        $this->register($option);

        $option = new org_tubepress_api_options_OptionDescriptor(org_tubepress_api_const_options_names_WordPress::WIDGET_SHORTCODE);
        $option->setDefaultValue('[tubepress thumbHeight=\'105\' thumbWidth=\'135\']');
        $this->register($option);
    }
}
