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
class tubepress_addons_core_impl_options_CoreOptionProvider extends tubepress_impl_options_AbstractOptionProvider
{
    /**
     * @var array
     */
    private $_embeddedPlayers = array();

    /**
     * @var array
     */
    private $_videoProviders = array();

    /**
     * @var array
     */
    private $_playerLocations = array();

    public function setPluggablePlayerLocations(array $players)
    {
        $this->_playerLocations = $players;
    }

    public function setPluggableEmbeddedPlayers(array $embeds)
    {
        $this->_embeddedPlayers = $embeds;
    }

    public function setPluggableVideoProviders(array $providers)
    {
        $this->_videoProviders = $providers;
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected function getMapOfOptionNamesToUntranslatedLabels()
    {
        return array(

            tubepress_api_const_options_names_Embedded::AUTONEXT        => 'Play videos sequentially without user intervention', //>(translatable)<
            tubepress_api_const_options_names_Embedded::AUTOPLAY        => 'Auto-play all videos',                               //>(translatable)<
            tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT => 'Max height (px)',                                    //>(translatable)<
            tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH  => 'Max width (px)',                                     //>(translatable)<
            tubepress_api_const_options_names_Embedded::ENABLE_JS_API   => 'Enable JavaScript API',                              //>(translatable)<
            tubepress_api_const_options_names_Embedded::LAZYPLAY        => '"Lazy" play videos',                                 //>(translatable)<
            tubepress_api_const_options_names_Embedded::LOOP            => 'Loop',                                               //>(translatable)<
            tubepress_api_const_options_names_Embedded::PLAYER_IMPL     => 'Implementation',                                     //>(translatable)<
            tubepress_api_const_options_names_Embedded::PLAYER_LOCATION => 'Play each video',                                    //>(translatable)<
            tubepress_api_const_options_names_Embedded::SHOW_INFO       => 'Show title and rating before video starts',          //>(translatable)<

            tubepress_api_const_options_names_Feed::ORDER_BY         => 'Order videos by',                               //>(translatable)<
            tubepress_api_const_options_names_Feed::PER_PAGE_SORT    => 'Per-page sort order',                           //>(translatable)<
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 'Maximum total videos to retrieve',              //>(translatable)<
            tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => 'Restrict search results to videos from author', //>(translatable)<
            tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST  => 'Video blacklist',                               //>(translatable)<

            tubepress_api_const_options_names_Meta::DATEFORMAT     => 'Date format',                //>(translatable)<
            tubepress_api_const_options_names_Meta::DESC_LIMIT     => 'Maximum description length', //>(translatable)<
            tubepress_api_const_options_names_Meta::RELATIVE_DATES => 'Use relative dates',         //>(translatable)<

            tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION  => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
            tubepress_api_const_options_names_Thumbs::FLUID_THUMBS     => 'Use "fluid" thumbnails',             //>(translatable)<
            tubepress_api_const_options_names_Thumbs::HQ_THUMBS        => 'Use high-quality thumbnails',        //>(translatable)<
            tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE   => 'Show pagination above thumbnails',   //>(translatable)<
            tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW   => 'Show pagination below thumbnails',   //>(translatable)<
            tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS    => 'Randomize thumbnail images',         //>(translatable)<
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => 'Thumbnails per page',                //>(translatable)<
            tubepress_api_const_options_names_Thumbs::THEME            => 'Theme',                              //>(translatable)<
            tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT     => 'Height (px) of thumbs',              //>(translatable)<
            tubepress_api_const_options_names_Thumbs::THUMB_WIDTH      => 'Width (px) of thumbs',               //>(translatable)<

            tubepress_api_const_options_names_Advanced::DEBUG_ON    => 'Enable debugging',   //>(translatable)<                                                                                                                                                                                                                                        //>(translatable)<
            tubepress_api_const_options_names_Advanced::HTTPS       => 'Enable HTTPS',       //>(translatable)<
            tubepress_api_const_options_names_Advanced::KEYWORD     => 'Shortcode keyword',  //>(translatable)<
            tubepress_api_const_options_names_Advanced::HTTP_METHOD => 'HTTP method',        //>(translatable)<

            tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR     => 'Cache cleaning factor',           //>(translatable)<
            tubepress_api_const_options_names_Cache::CACHE_DIR              => 'Cache directory',                 //>(translatable)<
            tubepress_api_const_options_names_Cache::CACHE_ENABLED          => 'Enable API cache',                //>(translatable)<
            tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS => 'Cache expiration time (seconds)', //>(translatable)<

            tubepress_api_const_options_names_Meta::AUTHOR      => 'Author',           //>(translatable)<
            tubepress_api_const_options_names_Meta::CATEGORY    => 'Category',         //>(translatable)<
            tubepress_api_const_options_names_Meta::DESCRIPTION => 'Description',      //>(translatable)<
            tubepress_api_const_options_names_Meta::ID          => 'ID',               //>(translatable)<
            tubepress_api_const_options_names_Meta::KEYWORDS    => 'Keywords',         //>(translatable)<
            tubepress_api_const_options_names_Meta::LENGTH      => 'Runtime',          //>(translatable)<
            tubepress_api_const_options_names_Meta::TITLE       => 'Title',            //>(translatable)<
            tubepress_api_const_options_names_Meta::UPLOADED    => 'Date posted',      //>(translatable)<
            tubepress_api_const_options_names_Meta::URL         => 'URL',              //>(translatable)<
            tubepress_api_const_options_names_Meta::VIEWS       => 'View count',       //>(translatable)<

            tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS => 'Only show options applicable to...', //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding untranslated label.
     */
    protected function getMapOfOptionNamesToUntranslatedDescriptions()
    {
        return array(

            tubepress_api_const_options_names_Advanced::DEBUG_ON    => 'If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.',  //>(translatable)<
            tubepress_api_const_options_names_Advanced::HTTPS       => 'Serve thumbnails and embedded video player over a secure connection.',  //>(translatable)<
            tubepress_api_const_options_names_Advanced::KEYWORD     => 'The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.', //>(translatable)<
            tubepress_api_const_options_names_Advanced::HTTP_METHOD => 'Defines the HTTP method used in most TubePress Ajax operations',  //>(translatable)<

            tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR     => 'If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.', //>(translatable)<
            tubepress_api_const_options_names_Cache::CACHE_DIR              => 'Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.', //>(translatable)<
            tubepress_api_const_options_names_Cache::CACHE_ENABLED          => 'Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.', //>(translatable)<
            tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS => 'Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).',   //>(translatable)<

            tubepress_api_const_options_names_Embedded::AUTONEXT        => 'When a video finishes, this will start playing the next video in the gallery.',  //>(translatable)<
            tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT => sprintf('Default is %s.', 390), //>(translatable)<
            tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH  => sprintf('Default is %s.', 640), //>(translatable)<
            tubepress_api_const_options_names_Embedded::ENABLE_JS_API   => 'Allow TubePress to communicate with the embedded video player via JavaScript. This incurs a very small performance overhead, but is required for some features.', //>(translatable)<
            tubepress_api_const_options_names_Embedded::LAZYPLAY        => 'Auto-play each video after thumbnail click.', //>(translatable)<
            tubepress_api_const_options_names_Embedded::LOOP            => 'Continue playing the video until the user stops it.', //>(translatable)<
            tubepress_api_const_options_names_Embedded::PLAYER_IMPL     => 'The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).', //>(translatable)<

            tubepress_api_const_options_names_Feed::ORDER_BY         => sprintf('Not all sort orders can be applied to all gallery types. See the <a href="%s" target="_blank">documentation</a> for more info.', "http://docs.tubepress.com/page/reference/options/core.html#orderby"),  //>(translatable)<
            tubepress_api_const_options_names_Feed::PER_PAGE_SORT    => 'Additional sort order applied to each individual page of a gallery',  //>(translatable)<
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP => 'This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.', //>(translatable)<
            tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => 'A YouTube or Vimeo user name. Only applies to search-based galleries.',      //>(translatable)<
            tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST  => 'A list of video IDs that should never be displayed.',  //>(translatable)<

            tubepress_api_const_options_names_Meta::DATEFORMAT     => sprintf('Set the textual formatting of date information for videos. See <a href="%s" target="_blank">date</a> for examples.', "http://php.net/date"),    //>(translatable)<
            tubepress_api_const_options_names_Meta::DESC_LIMIT     => 'Maximum number of characters to display in video descriptions. Set to 0 for no limit.', //>(translatable)<
            tubepress_api_const_options_names_Meta::RELATIVE_DATES => 'e.g. "yesterday" instead of "November 3, 1980".',  //>(translatable)<

            tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION  => sprintf('<a href="%s" target="_blank">Ajax</a>-enabled pagination', "http://wikipedia.org/wiki/Ajax_(programming)"),  //>(translatable)<
            tubepress_api_const_options_names_Thumbs::FLUID_THUMBS     => 'Dynamically set thumbnail spacing based on the width of their container.', //>(translatable)<
            tubepress_api_const_options_names_Thumbs::HQ_THUMBS        => 'Note: this option cannot be used with the "randomize thumbnails" feature.', //>(translatable)<
            tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE   => 'Only applies to galleries that span multiple pages.', //>(translatable)<
            tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW   => 'Only applies to galleries that span multiple pages.', //>(translatable)<
            tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS    => 'Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.', //>(translatable)<
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE => sprintf('Default is %s. Maximum is %s.', 20, 50),     //>(translatable)<
            tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT     => sprintf('Default is %s.', 90),   //>(translatable)<
            tubepress_api_const_options_names_Thumbs::THUMB_WIDTH      => sprintf('Default is %s.', 120),  //>(translatable)<
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding default values.
     */
    protected function getMapOfOptionNamesToDefaultValues()
    {
        return array(

            tubepress_api_const_options_names_Advanced::DEBUG_ON                     => true,
            tubepress_api_const_options_names_Advanced::GALLERY_ID                   => null,
            tubepress_api_const_options_names_Advanced::HTTP_METHOD                  => 'GET',
            tubepress_api_const_options_names_Advanced::HTTPS                        => false,
            tubepress_api_const_options_names_Advanced::KEYWORD                      => 'tubepress',
            tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR              => 20,
            tubepress_api_const_options_names_Cache::CACHE_DIR                       => null,
            tubepress_api_const_options_names_Cache::CACHE_ENABLED                   => true,
            tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS          => 3600,
            tubepress_api_const_options_names_Embedded::AUTONEXT                     => true,
            tubepress_api_const_options_names_Embedded::AUTOPLAY                     => false,
            tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT              => 390,
            tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH               => 640,
            tubepress_api_const_options_names_Embedded::ENABLE_JS_API                => true,
            tubepress_api_const_options_names_Embedded::LAZYPLAY                     => true,
            tubepress_api_const_options_names_Embedded::LOOP                         => false,
            tubepress_api_const_options_names_Embedded::PLAYER_IMPL                  => tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED,
            tubepress_api_const_options_names_Embedded::PLAYER_LOCATION              => 'normal',
            tubepress_api_const_options_names_Embedded::SEQUENCE                     => null,
            tubepress_api_const_options_names_Embedded::SHOW_INFO                    => false,
            tubepress_api_const_options_names_Feed::ORDER_BY                         => tubepress_api_const_options_values_OrderByValue::DEFAULTT,
            tubepress_api_const_options_names_Feed::PER_PAGE_SORT                    => tubepress_api_const_options_values_PerPageSortValue::NONE,
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP                 => 0,
            tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER                 => null,
            tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST                  => null,
            tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER     => null,
            tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY => false,
            tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL  => null,
            tubepress_api_const_options_names_Meta::AUTHOR                           => false,
            tubepress_api_const_options_names_Meta::CATEGORY                         => false,
            tubepress_api_const_options_names_Meta::DATEFORMAT                       => 'M j, Y',
            tubepress_api_const_options_names_Meta::DESC_LIMIT                       => 80,
            tubepress_api_const_options_names_Meta::DESCRIPTION                      => false,
            tubepress_api_const_options_names_Meta::ID                               => false,
            tubepress_api_const_options_names_Meta::KEYWORDS                         => false,
            tubepress_api_const_options_names_Meta::LENGTH                           => true,
            tubepress_api_const_options_names_Meta::RELATIVE_DATES                   => false,
            tubepress_api_const_options_names_Meta::TITLE                            => true,
            tubepress_api_const_options_names_Meta::UPLOADED                         => false,
            tubepress_api_const_options_names_Meta::URL                              => false,
            tubepress_api_const_options_names_Meta::VIEWS                            => true,
            tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS => null,
            tubepress_api_const_options_names_Output::GALLERY_SOURCE                 => tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
            tubepress_api_const_options_names_Output::OUTPUT                         => null,
            tubepress_api_const_options_names_Output::VIDEO                          => null,
            tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION                => false,
            tubepress_api_const_options_names_Thumbs::FLUID_THUMBS                   => true,
            tubepress_api_const_options_names_Thumbs::HQ_THUMBS                      => false,
            tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE                 => true,
            tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW                 => true,
            tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS                  => true,
            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE               => 20,
            tubepress_api_const_options_names_Thumbs::THEME                          => 'tubepress/default',
            tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT                   => 90,
            tubepress_api_const_options_names_Thumbs::THUMB_WIDTH                    => 120,
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding valid value regexes.
     */
    protected function getMapOfOptionNamesToValidValueRegexes()
    {
        return array(

            tubepress_api_const_options_names_Advanced::GALLERY_ID   => '/\w+/',
            tubepress_api_const_options_names_Advanced::KEYWORD      => '/\w+/',
            tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER => '/\w*/',
        );
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    protected function getOptionNamesThatCannotBeSetViaShortcode()
    {
        return array(

            tubepress_api_const_options_names_Advanced::KEYWORD
        );
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               that cannot be set via shortcode.
     */
    protected function getOptionsNamesThatShouldNotBePersisted()
    {
        return array(

            tubepress_api_const_options_names_Advanced::GALLERY_ID,
            tubepress_api_const_options_names_Embedded::SEQUENCE,
            tubepress_api_const_options_names_Output::OUTPUT,
            tubepress_api_const_options_names_Output::VIDEO,
        );
    }

    /**
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding fixed acceptable values.
     */
    protected function getMapOfOptionNamesToFixedAcceptableValues()
    {
        return array(

            tubepress_api_const_options_names_Advanced::HTTP_METHOD => array(

                'GET'  => 'GET',
                'POST' => 'POST'
            ),

            tubepress_api_const_options_names_Feed::ORDER_BY => array(

                tubepress_api_const_options_values_OrderByValue::DEFAULTT       => 'default',                         //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT  => 'comment count',                   //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::NEWEST         => 'date published (newest first)',   //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::OLDEST         => 'date published (oldest first)',   //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::DURATION       => 'length',                          //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::POSITION       => 'position in a playlist',          //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::REV_POSITION   => 'reversed position in a playlist', //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::RANDOM         => 'randomly',                        //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::RATING         => 'rating',                          //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::RELEVANCE      => 'relevance',                       //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::TITLE          => 'title',                           //>(translatable)<
                tubepress_api_const_options_values_OrderByValue::VIEW_COUNT     => 'view count',                      //>(translatable)<
            ),
            
            tubepress_api_const_options_names_Feed::PER_PAGE_SORT => array(

                tubepress_api_const_options_values_PerPageSortValue::COMMENT_COUNT  => 'comment count',                 //>(translatable)<
                tubepress_api_const_options_values_PerPageSortValue::NEWEST         => 'date published (newest first)', //>(translatable)<
                tubepress_api_const_options_values_PerPageSortValue::OLDEST         => 'date published (oldest first)', //>(translatable)<
                tubepress_api_const_options_values_PerPageSortValue::DURATION       => 'length',                        //>(translatable)<
                tubepress_api_const_options_values_PerPageSortValue::NONE           => 'none',                          //>(translatable)<
                tubepress_api_const_options_values_PerPageSortValue::RANDOM         => 'random',                        //>(translatable)<
                tubepress_api_const_options_values_PerPageSortValue::RATING         => 'rating',                        //>(translatable)<
                tubepress_api_const_options_values_PerPageSortValue::TITLE          => 'title',                         //>(translatable)<
                tubepress_api_const_options_values_PerPageSortValue::VIEW_COUNT     => 'view count',                    //>(translatable)<
            ),
        );
    }

    /**
     * @return array An array, which may be empty but not null, of option names
     *               to that have
     */
    protected function getOptionNamesWithDynamicDiscreteAcceptableValues()
    {
        return array(

            tubepress_api_const_options_names_Embedded::PLAYER_LOCATION,
            tubepress_api_const_options_names_Embedded::PLAYER_IMPL,
            tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER,
            tubepress_api_const_options_names_Thumbs::THEME,
        );
    }

    /**
     * @param $optionName string The option name.
     *
     * @return array An associative array, which may be empty but not null, of option names
     *               to their corresponding dynamic acceptable values.
     */
    protected function getDynamicDiscreteAcceptableValuesForOption($optionName)
    {
        switch ($optionName) {
            
            case tubepress_api_const_options_names_Embedded::PLAYER_LOCATION:

                $toReturn = array();

                /**
                 * @var $playerLocation tubepress_spi_player_PluggablePlayerLocationService
                 */
                foreach ($this->_playerLocations as $playerLocation) {

                    $toReturn[$playerLocation->getName()] = $playerLocation->getFriendlyName();
                }

                asort($toReturn);

                return $toReturn;
            
            case tubepress_api_const_options_names_Embedded::PLAYER_IMPL:
                
                return $this->_getValidPlayerImplementations();
            
            case tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER:
                
                return $this->_getValidVideoProviderNames();
            
            case tubepress_api_const_options_names_Thumbs::THEME:
                
                return $this->_getValidThemeValues();
            
            default:
                
                return array();
        }
    }

    /**
     * @return string[] An array, which may be empty but not null, of Pro option names from this provider.
     */
    protected function getAllProOptionNames()
    {
        return array(
          
            tubepress_api_const_options_names_Advanced::HTTPS,
            tubepress_api_const_options_names_Embedded::AUTONEXT,
            tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION,
            tubepress_api_const_options_names_Thumbs::HQ_THUMBS,
        );
    }

    protected function getOptionNamesOfNonNegativeIntegers()
    {
        return array(

            tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR,
            tubepress_api_const_options_names_Meta::DESC_LIMIT,
            tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP,
        );
    }

    protected function getOptionNamesOfPositiveIntegers()
    {
        return array(

            tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE,
            tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS,
            tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT,
            tubepress_api_const_options_names_Thumbs::THUMB_WIDTH,
            tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT,
            tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH,
        );
    }

    private function _getValidThemeValues()
    {
        $themeHandler = tubepress_impl_patterns_sl_ServiceLocator::getThemeHandler();
        $themeNames   = $themeHandler->getMapOfAllThemeNamesToTitles();

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
         * @var $videoProvider tubepress_spi_provider_PluggableVideoProviderService
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
         * @var $embeddedImpl tubepress_spi_embedded_PluggableEmbeddedPlayerService
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

            tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED => 'Provider default',  //>(translatable)<

        ), $detected);
    }
}