<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_core_impl_options_CoreOptionProvider implements tubepress_core_api_options_EasyProviderInterface
{
    /**
     * @var tubepress_core_api_embedded_EmbeddedProviderInterface[]
     */
    private $_embeddedPlayers = array();

    /**
     * @var array
     */
    private $_videoProviders = array();

    /**
     * @var tubepress_core_api_player_PlayerLocationInterface[]
     */
    private $_playerLocations = array();

    /**
     * @var tubepress_core_api_theme_ThemeLibraryInterface
     */
    private $_themeLibrary;

    public function setPlayerLocations(array $players)
    {
        $this->_playerLocations = $players;
    }

    public function setEmbeddedProviders(array $embeds)
    {
        $this->_embeddedPlayers = $embeds;
    }

    public function setVideoProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }

    public function __construct(tubepress_core_api_theme_ThemeLibraryInterface $themeLibrary)
    {
        $this->_themeLibrary = $themeLibrary;
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    public function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array(

            tubepress_core_api_const_options_Names::AUTONEXT        => 'Play videos sequentially without user intervention', //>(translatable)<
            tubepress_core_api_const_options_Names::AUTOPLAY        => 'Auto-play all videos',                               //>(translatable)<
            tubepress_core_api_const_options_Names::EMBEDDED_HEIGHT => 'Max height (px)',                                    //>(translatable)<
            tubepress_core_api_const_options_Names::EMBEDDED_WIDTH  => 'Max width (px)',                                     //>(translatable)<
            tubepress_core_api_const_options_Names::ENABLE_JS_API   => 'Enable JavaScript API',                              //>(translatable)<
            tubepress_core_api_const_options_Names::LAZYPLAY        => '"Lazy" play videos',                                 //>(translatable)<
            tubepress_core_api_const_options_Names::LOOP            => 'Loop',                                               //>(translatable)<
            tubepress_core_api_const_options_Names::PLAYER_IMPL     => 'Implementation',                                     //>(translatable)<
            tubepress_core_api_const_options_Names::PLAYER_LOCATION => 'Play each video',                                    //>(translatable)<
            tubepress_core_api_const_options_Names::SHOW_INFO       => 'Show title and rating before video starts',          //>(translatable)<

            tubepress_core_api_const_options_Names::ORDER_BY         => 'Order videos by',                               //>(translatable)<
            tubepress_core_api_const_options_Names::PER_PAGE_SORT    => 'Per-page sort order',                           //>(translatable)<
            tubepress_core_api_const_options_Names::RESULT_COUNT_CAP => 'Maximum total videos to retrieve',              //>(translatable)<
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER => 'Restrict search results to videos from author', //>(translatable)<
            tubepress_core_api_const_options_Names::VIDEO_BLACKLIST  => 'Video blacklist',                               //>(translatable)<

            tubepress_core_api_const_options_Names::DATEFORMAT     => 'Date format',                //>(translatable)<
            tubepress_core_api_const_options_Names::DESC_LIMIT     => 'Maximum description length', //>(translatable)<
            tubepress_core_api_const_options_Names::RELATIVE_DATES => 'Use relative dates',         //>(translatable)<

            tubepress_core_api_const_options_Names::AJAX_PAGINATION  => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
            tubepress_core_api_const_options_Names::FLUID_THUMBS     => 'Use "fluid" thumbnails',             //>(translatable)<
            tubepress_core_api_const_options_Names::HQ_THUMBS        => 'Use high-quality thumbnails',        //>(translatable)<
            tubepress_core_api_const_options_Names::PAGINATE_ABOVE   => 'Show pagination above thumbnails',   //>(translatable)<
            tubepress_core_api_const_options_Names::PAGINATE_BELOW   => 'Show pagination below thumbnails',   //>(translatable)<
            tubepress_core_api_const_options_Names::RANDOM_THUMBS    => 'Randomize thumbnail images',         //>(translatable)<
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => 'Thumbnails per page',                //>(translatable)<
            tubepress_core_api_const_options_Names::THEME            => 'Theme',                              //>(translatable)<
            tubepress_core_api_const_options_Names::THUMB_HEIGHT     => 'Height (px) of thumbs',              //>(translatable)<
            tubepress_core_api_const_options_Names::THUMB_WIDTH      => 'Width (px) of thumbs',               //>(translatable)<

            tubepress_core_api_const_options_Names::DEBUG_ON    => 'Enable debugging',   //>(translatable)<                                                                                                                                                                                                                                        //>(translatable)<
            tubepress_core_api_const_options_Names::HTTPS       => 'Enable HTTPS',       //>(translatable)<
            tubepress_core_api_const_options_Names::KEYWORD     => 'Shortcode keyword',  //>(translatable)<
            tubepress_core_api_const_options_Names::HTTP_METHOD => 'HTTP method',        //>(translatable)<

            tubepress_core_api_const_options_Names::CACHE_CLEAN_FACTOR     => 'Cache cleaning factor',           //>(translatable)<
            tubepress_core_api_const_options_Names::CACHE_DIR              => 'Cache directory',                 //>(translatable)<
            tubepress_core_api_const_options_Names::CACHE_ENABLED          => 'Enable API cache',                //>(translatable)<
            tubepress_core_api_const_options_Names::CACHE_LIFETIME_SECONDS => 'Cache expiration time (seconds)', //>(translatable)<

            tubepress_core_api_const_options_Names::AUTHOR      => 'Author',           //>(translatable)<
            tubepress_core_api_const_options_Names::CATEGORY    => 'Category',         //>(translatable)<
            tubepress_core_api_const_options_Names::DESCRIPTION => 'Description',      //>(translatable)<
            tubepress_core_api_const_options_Names::ID          => 'ID',               //>(translatable)<
            tubepress_core_api_const_options_Names::KEYWORDS    => 'Keywords',         //>(translatable)<
            tubepress_core_api_const_options_Names::LENGTH      => 'Runtime',          //>(translatable)<
            tubepress_core_api_const_options_Names::TITLE       => 'Title',            //>(translatable)<
            tubepress_core_api_const_options_Names::UPLOADED    => 'Date posted',      //>(translatable)<
            tubepress_core_api_const_options_Names::URL         => 'URL',              //>(translatable)<
            tubepress_core_api_const_options_Names::VIEWS       => 'View count',       //>(translatable)<

            tubepress_core_api_const_options_Names::DISABLED_OPTIONS_PAGE_PARTICIPANTS => 'Only show options applicable to...', //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    public function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array(

            tubepress_core_api_const_options_Names::DEBUG_ON    => 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.',  //>(translatable)<
            tubepress_core_api_const_options_Names::HTTPS       => 'Serve thumbnails and embedded video player over a secure connection.',  //>(translatable)<
            tubepress_core_api_const_options_Names::KEYWORD     => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.', //>(translatable)<
            tubepress_core_api_const_options_Names::HTTP_METHOD => 'Defines the HTTP method used in most TubePress Ajax operations',  //>(translatable)<

            tubepress_core_api_const_options_Names::CACHE_CLEAN_FACTOR     => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.', //>(translatable)<
            tubepress_core_api_const_options_Names::CACHE_DIR              => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.', //>(translatable)<
            tubepress_core_api_const_options_Names::CACHE_ENABLED          => 'Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.', //>(translatable)<
            tubepress_core_api_const_options_Names::CACHE_LIFETIME_SECONDS => 'Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).',   //>(translatable)<

            tubepress_core_api_const_options_Names::AUTONEXT        => 'When a video finishes, this will start playing the next video in the gallery.',  //>(translatable)<
            tubepress_core_api_const_options_Names::EMBEDDED_HEIGHT => sprintf('Default is %s.', 390), //>(translatable)<
            tubepress_core_api_const_options_Names::EMBEDDED_WIDTH  => sprintf('Default is %s.', 640), //>(translatable)<
            tubepress_core_api_const_options_Names::ENABLE_JS_API   => 'Allow TubePress to communicate with the embedded video player via JavaScript. This incurs a very small performance overhead, but is required for some features.', //>(translatable)<
            tubepress_core_api_const_options_Names::LAZYPLAY        => 'Auto-play each video after thumbnail click.', //>(translatable)<
            tubepress_core_api_const_options_Names::LOOP            => 'Continue playing the video until the user stops it.', //>(translatable)<
            tubepress_core_api_const_options_Names::PLAYER_IMPL     => 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).', //>(translatable)<

            tubepress_core_api_const_options_Names::ORDER_BY         => sprintf('Not all sort orders can be applied to all gallery types. See the <a href="%s" target="_blank">documentation</a> for more info.', "http://docs.tubepress.com/page/reference/options/core.html#orderby"),  //>(translatable)<
            tubepress_core_api_const_options_Names::PER_PAGE_SORT    => 'Additional sort order applied to each individual page of a gallery',  //>(translatable)<
            tubepress_core_api_const_options_Names::RESULT_COUNT_CAP => 'This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.', //>(translatable)<
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER => 'A YouTube or Vimeo user name. Only applies to search-based galleries.',      //>(translatable)<
            tubepress_core_api_const_options_Names::VIDEO_BLACKLIST  => 'A list of video IDs that should never be displayed.',  //>(translatable)<

            tubepress_core_api_const_options_Names::DATEFORMAT     => sprintf('Set the textual formatting of date information for videos. See <a href="%s" target="_blank">date</a> for examples.', "http://php.net/date"),    //>(translatable)<
            tubepress_core_api_const_options_Names::DESC_LIMIT     => 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.', //>(translatable)<
            tubepress_core_api_const_options_Names::RELATIVE_DATES => 'e.g. "yesterday" instead of "November 3, 1980".',  //>(translatable)<

            tubepress_core_api_const_options_Names::AJAX_PAGINATION  => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
            tubepress_core_api_const_options_Names::FLUID_THUMBS     => 'Dynamically set thumbnail spacing based on the width of their container.', //>(translatable)<
            tubepress_core_api_const_options_Names::HQ_THUMBS        => 'Note: this option cannot be used with the "randomize thumbnails" feature.', //>(translatable)<
            tubepress_core_api_const_options_Names::PAGINATE_ABOVE   => 'Only applies to galleries that span multiple pages.', //>(translatable)<
            tubepress_core_api_const_options_Names::PAGINATE_BELOW   => 'Only applies to galleries that span multiple pages.', //>(translatable)<
            tubepress_core_api_const_options_Names::RANDOM_THUMBS    => 'Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.', //>(translatable)<
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE => sprintf('Default is %s. Maximum is %s.', 20, 50),     //>(translatable)<
            tubepress_core_api_const_options_Names::THUMB_HEIGHT     => sprintf('Default is %s.', 90),   //>(translatable)<
            tubepress_core_api_const_options_Names::THUMB_WIDTH      => sprintf('Default is %s.', 120),  //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     */
    public function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_core_api_const_options_Names::DEBUG_ON                     => true,
            tubepress_core_api_const_options_Names::GALLERY_ID                   => null,
            tubepress_core_api_const_options_Names::HTTP_METHOD                  => 'GET',
            tubepress_core_api_const_options_Names::HTTPS                        => false,
            tubepress_core_api_const_options_Names::KEYWORD                      => 'tubepress',
            tubepress_core_api_const_options_Names::CACHE_CLEAN_FACTOR              => 20,
            tubepress_core_api_const_options_Names::CACHE_DIR                       => null,
            tubepress_core_api_const_options_Names::CACHE_ENABLED                   => true,
            tubepress_core_api_const_options_Names::CACHE_LIFETIME_SECONDS          => 3600,
            tubepress_core_api_const_options_Names::AUTONEXT                     => true,
            tubepress_core_api_const_options_Names::AUTOPLAY                     => false,
            tubepress_core_api_const_options_Names::EMBEDDED_HEIGHT              => 390,
            tubepress_core_api_const_options_Names::EMBEDDED_WIDTH               => 640,
            tubepress_core_api_const_options_Names::ENABLE_JS_API                => true,
            tubepress_core_api_const_options_Names::LAZYPLAY                     => true,
            tubepress_core_api_const_options_Names::LOOP                         => false,
            tubepress_core_api_const_options_Names::PLAYER_IMPL                  => tubepress_core_api_const_options_ValidValues::EMBEDDED_IMPL_PROVIDER_BASED,
            tubepress_core_api_const_options_Names::PLAYER_LOCATION              => 'normal',
            tubepress_core_api_const_options_Names::SEQUENCE                     => null,
            tubepress_core_api_const_options_Names::SHOW_INFO                    => false,
            tubepress_core_api_const_options_Names::ORDER_BY                         => tubepress_core_api_const_options_ValidValues::ORDER_BY_DEFAULT,
            tubepress_core_api_const_options_Names::PER_PAGE_SORT                    => tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_NONE,
            tubepress_core_api_const_options_Names::RESULT_COUNT_CAP                 => 0,
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER                 => null,
            tubepress_core_api_const_options_Names::VIDEO_BLACKLIST                  => null,
            tubepress_core_api_const_options_Names::SEARCH_PROVIDER     => null,
            tubepress_core_api_const_options_Names::SEARCH_RESULTS_ONLY => false,
            tubepress_core_api_const_options_Names::SEARCH_RESULTS_URL  => null,
            tubepress_core_api_const_options_Names::AUTHOR                           => false,
            tubepress_core_api_const_options_Names::CATEGORY                         => false,
            tubepress_core_api_const_options_Names::DATEFORMAT                       => 'M j, Y',
            tubepress_core_api_const_options_Names::DESC_LIMIT                       => 80,
            tubepress_core_api_const_options_Names::DESCRIPTION                      => false,
            tubepress_core_api_const_options_Names::ID                               => false,
            tubepress_core_api_const_options_Names::KEYWORDS                         => false,
            tubepress_core_api_const_options_Names::LENGTH                           => true,
            tubepress_core_api_const_options_Names::RELATIVE_DATES                   => false,
            tubepress_core_api_const_options_Names::TITLE                            => true,
            tubepress_core_api_const_options_Names::UPLOADED                         => false,
            tubepress_core_api_const_options_Names::URL                              => false,
            tubepress_core_api_const_options_Names::VIEWS                            => true,
            tubepress_core_api_const_options_Names::DISABLED_OPTIONS_PAGE_PARTICIPANTS => null,
            tubepress_core_api_const_options_Names::GALLERY_SOURCE                 => tubepress_youtube_api_const_options_Values::YOUTUBE_MOST_POPULAR,
            tubepress_core_api_const_options_Names::OUTPUT                         => null,
            tubepress_core_api_const_options_Names::VIDEO                          => null,
            tubepress_core_api_const_options_Names::AJAX_PAGINATION                => false,
            tubepress_core_api_const_options_Names::FLUID_THUMBS                   => true,
            tubepress_core_api_const_options_Names::HQ_THUMBS                      => false,
            tubepress_core_api_const_options_Names::PAGINATE_ABOVE                 => true,
            tubepress_core_api_const_options_Names::PAGINATE_BELOW                 => true,
            tubepress_core_api_const_options_Names::RANDOM_THUMBS                  => true,
            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE               => 20,
            tubepress_core_api_const_options_Names::THEME                          => 'tubepress/default',
            tubepress_core_api_const_options_Names::THUMB_HEIGHT                   => 90,
            tubepress_core_api_const_options_Names::THUMB_WIDTH                    => 120,
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding valid value regexes.
     */
    public function getMapOfOptionNamesToValidValueRegexes()
    {
        return array(

            tubepress_core_api_const_options_Names::GALLERY_ID   => '/\w+/',
            tubepress_core_api_const_options_Names::KEYWORD      => '/\w+/',
            tubepress_core_api_const_options_Names::SEARCH_ONLY_USER => '/\w*/',
        );
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    public function getOptionNamesThatCannotBeSetViaShortcode()
    {
        return array(

            tubepress_core_api_const_options_Names::KEYWORD
        );
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    public function getOptionsNamesThatShouldNotBePersisted()
    {
        return array(

            tubepress_core_api_const_options_Names::GALLERY_ID,
            tubepress_core_api_const_options_Names::SEQUENCE,
            tubepress_core_api_const_options_Names::OUTPUT,
            tubepress_core_api_const_options_Names::VIDEO,
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding fixed acceptable values.
     */
    public function getMapOfOptionNamesToFixedAcceptableValues()
    {
        return array(

            tubepress_core_api_const_options_Names::HTTP_METHOD => array(

                'GET'  => 'GET',
                'POST' => 'POST'
            ),

            tubepress_core_api_const_options_Names::ORDER_BY => array(

                tubepress_core_api_const_options_ValidValues::ORDER_BY_DEFAULT       => 'default',                         //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_COMMENT_COUNT  => 'comment count',                   //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_NEWEST         => 'date published (newest first)',   //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_OLDEST         => 'date published (oldest first)',   //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_DURATION       => 'length',                          //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_POSITION       => 'position in a playlist',          //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_REV_POSITION   => 'reversed position in a playlist', //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_RANDOM         => 'randomly',                        //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_RATING         => 'rating',                          //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_RELEVANCE      => 'relevance',                       //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_TITLE          => 'title',                           //>(translatable)<
                tubepress_core_api_const_options_ValidValues::ORDER_BY_VIEW_COUNT     => 'view count',                      //>(translatable)<
            ),
            
            tubepress_core_api_const_options_Names::PER_PAGE_SORT => array(

                tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_COMMENT_COUNT  => 'comment count',                 //>(translatable)<
                tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_NEWEST         => 'date published (newest first)', //>(translatable)<
                tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_OLDEST         => 'date published (oldest first)', //>(translatable)<
                tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_DURATION       => 'length',                        //>(translatable)<
                tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_NONE           => 'none',                          //>(translatable)<
                tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_RANDOM         => 'random',                        //>(translatable)<
                tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_RATING         => 'rating',                        //>(translatable)<
                tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_TITLE          => 'title',                         //>(translatable)<
                tubepress_core_api_const_options_ValidValues::PER_PAGE_SORT_VIEW_COUNT     => 'view count',                    //>(translatable)<
            ),
        );
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               to that have
     */
    public function getOptionNamesWithDynamicDiscreteAcceptableValues()
    {
        return array(

            tubepress_core_api_const_options_Names::PLAYER_LOCATION,
            tubepress_core_api_const_options_Names::PLAYER_IMPL,
            tubepress_core_api_const_options_Names::SEARCH_PROVIDER,
            tubepress_core_api_const_options_Names::THEME,
        );
    }

    /**
     * @param $optionName string The option name.
     *
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding dynamic acceptable values.
     */
    public function getDynamicDiscreteAcceptableValuesForOption($optionName)
    {
        switch ($optionName) {
            
            case tubepress_core_api_const_options_Names::PLAYER_LOCATION:

                $toReturn = array();

                /**
                 * @var $playerLocation tubepress_core_api_player_PlayerLocationInterface
                 */
                foreach ($this->_playerLocations as $playerLocation) {

                    $toReturn[$playerLocation->getName()] = $playerLocation->getFriendlyName();
                }

                asort($toReturn);

                return $toReturn;
            
            case tubepress_core_api_const_options_Names::PLAYER_IMPL:
                
                return $this->_getValidPlayerImplementations();
            
            case tubepress_core_api_const_options_Names::SEARCH_PROVIDER:
                
                return $this->_getValidVideoProviderNames();
            
            case tubepress_core_api_const_options_Names::THEME:
                
                return $this->_getValidThemeValues();
            
            default:
                
                return array();
        }
    }

    /**
     * @return string[] An array, which may be empty but not null, of Pro option names from this provider.
     */
    public function getAllProOptionNames()
    {
        return array(
          
            tubepress_core_api_const_options_Names::HTTPS,
            tubepress_core_api_const_options_Names::AUTONEXT,
            tubepress_core_api_const_options_Names::AJAX_PAGINATION,
            tubepress_core_api_const_options_Names::HQ_THUMBS,
        );
    }

    public function getOptionNamesOfNonNegativeIntegers()
    {
        return array(

            tubepress_core_api_const_options_Names::CACHE_CLEAN_FACTOR,
            tubepress_core_api_const_options_Names::DESC_LIMIT,
            tubepress_core_api_const_options_Names::RESULT_COUNT_CAP,
        );
    }

    public function getOptionNamesOfPositiveIntegers()
    {
        return array(

            tubepress_core_api_const_options_Names::RESULTS_PER_PAGE,
            tubepress_core_api_const_options_Names::CACHE_LIFETIME_SECONDS,
            tubepress_core_api_const_options_Names::THUMB_HEIGHT,
            tubepress_core_api_const_options_Names::THUMB_WIDTH,
            tubepress_core_api_const_options_Names::EMBEDDED_HEIGHT,
            tubepress_core_api_const_options_Names::EMBEDDED_WIDTH,
        );
    }

    private function _getValidThemeValues()
    {
        $themeNames = $this->_themeLibrary->getMapOfAllThemeNamesToTitles();

        ksort($themeNames);

        return $themeNames;
    }

    private function _getValidVideoProviderNames()
    {
        $toReturn = array_keys($this->_getValidProviderNamesToFriendlyNames());

        asort($toReturn);

        return $toReturn;
    }

    private function _getValidProviderNamesToFriendlyNames()
    {
        $toReturn = array();

        /**
         * @var $videoProvider tubepress_core_api_provider_VideoProviderInterface
         */
        foreach ($this->_videoProviders as $videoProvider) {

            $toReturn[$videoProvider->getName()] = $videoProvider->getFriendlyName();
        }

        return $toReturn;
    }

    private function _getValidPlayerImplementations()
    {
        $providerNames = $this->_getValidVideoProviderNames();
        $detected      = array();

        /**
         * @var $embeddedImpl tubepress_core_api_embedded_EmbeddedProviderInterface
         */
        foreach ($this->_embeddedPlayers as $embeddedImpl) {

            /**
             * If the embedded player service's name does not match a registered provider name,
             * it must be non provider based, so let's add it.
             */
            if (! in_array($embeddedImpl->getName(), $providerNames)) {

                $detected[$embeddedImpl->getName()] = $embeddedImpl->getFriendlyName();
            }
        }

        asort($detected);

        return array_merge(array(

            tubepress_core_api_const_options_ValidValues::EMBEDDED_IMPL_PROVIDER_BASED => 'Provider default',  //>(translatable)<

        ), $detected);
    }
}