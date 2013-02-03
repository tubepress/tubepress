<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_plugins_core_Core
{
    public static function init()
    {
        /*
         * Register the core TubePress options.
         */
        self::_registerCoreOptions();

        /*
         * Make sure the User Content Directory exists.
         */
        self::_ensureTubePressContentDirectoryExists();

        /*
         * Register the core event handlers.
         */
        self::_registerEventListeners();
    }

    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /******* OPTION REGISTRATION *******************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/

    private static function _registerCoreOptions()
    {
        $_regexPositiveInteger    = '/[1-9][0-9]{0,6}/';
        $_regexNonNegativeInteger = '/0|[1-9][0-9]{0,6}/';
        $_regexWordChars          = '/\w+/';

        $odr = tubepress_impl_patterns_sl_ServiceLocator::getOptionDescriptorReference();

        /**
         * ADVANCED OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $option->setDefaultValue(true);
        $option->setLabel('Enable debugging');                                                                                                                                                                                                                                                         //>(translatable)<
        $option->setDescription('If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.');  //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::HTTPS);
        $option->setDefaultValue(false);
        $option->setLabel('Enable HTTPS');                                                                //>(translatable)<
        $option->setDescription('Serve thumbnails and embedded video player over a secure connection.');  //>(translatable)<
        $option->setBoolean();
        $option->setProOnly();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::GALLERY_ID);
        $option->setValidValueRegex($_regexWordChars);
        $option->setDoNotPersist();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::KEYWORD);
        $option->setDefaultValue('tubepress');
        $option->setLabel('Shortcode keyword');                                                                                             //>(translatable)<
        $option->setDescription('The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.'); //>(translatable)<
        $option->setValidValueRegex($_regexWordChars);
        $option->setCannotBeSetViaShortcode();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::HTTP_METHOD);
        $option->setDefaultValue(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET);
        $option->setLabel('HTTP method');                                                           //>(translatable)<
        $option->setDescription('Defines the HTTP method used in most TubePress Ajax operations');  //>(translatable)<
        $option->setAcceptableValues(array(

            ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET => ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET,
            ehough_shortstop_api_HttpRequest::HTTP_METHOD_POST => ehough_shortstop_api_HttpRequest::HTTP_METHOD_POST,
        ));
        $odr->registerOptionDescriptor($option);



        /**
         * CACHE OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR);
        $option->setDefaultValue(20);
        $option->setLabel('Cache cleaning factor');                                                                                             //>(translatable)<
        $option->setDescription('If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.'); //>(translatable)<
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_DIR);
        $option->setLabel('Cache directory');                                                                                                                //>(translatable)<
        $option->setDescription('Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.'); //>(translatable)<
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_ENABLED);
        $option->setDefaultValue(false);
        $option->setLabel('Enable API cache');                                                                                                                    //>(translatable)<
        $option->setDescription('Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS);
        $option->setDefaultValue(3600);
        $option->setLabel('Cache expiration time (seconds)');                                                                                   //>(translatable)<
        $option->setDescription('Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).');   //>(translatable)<
        $option->setValidValueRegex($_regexPositiveInteger);
        $odr->registerOptionDescriptor($option);



        /**
         * EMBEDDED PLAYER OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::AUTONEXT);
        $option->setLabel('Play videos sequentially without user intervention');                                   //>(translatable)<
        $option->setDescription('When a video finishes, this will start playing the next video in the gallery.');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $option->setProOnly();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $option->setLabel('Auto-play all videos');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);
        $option->setDefaultValue(350);
        $option->setLabel('Max height (px)');       //>(translatable)<
        $option->setDescription('Default is 350.'); //>(translatable)<
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $option->setDefaultValue(425);
        $option->setLabel('Max width (px)');        //>(translatable)<
        $option->setDescription('Default is 425.'); //>(translatable)<
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
        $option->setDefaultValue(true);
        $option->setLabel('Enable JavaScript API');       //>(translatable)<
        $option->setDescription('Allow TubePress to communicate with the embedded video player via JavaScript. This incurs a very small performance overhead, but is required for some features.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::LAZYPLAY);
        $option->setDefaultValue(true);
        $option->setLabel('"Lazy" play videos');                                //>(translatable)<
        $option->setDescription('Auto-play each video after thumbnail click.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::LOOP);
        $option->setDefaultValue(false);
        $option->setLabel('Loop');                                                     //>(translatable)<
        $option->setDescription('Continue playing the video until the user stops it.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
        $option->setDefaultValue(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);
        $option->setLabel('Implementation');                                                                                   //>(translatable)<
        $option->setDescription('The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).'); //>(translatable)<
        $option->setAcceptableValuesCallback(array('tubepress_plugins_core_Core', '_callbackGetValidPlayerImplementations'));
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $option->setLabel('Play each video');                                                                                 //>(translatable)<
        $option->setDefaultValue('normal');
        $option->setAcceptableValuesCallback(array('tubepress_plugins_core_Core', '_callbackGetValidPlayerLocations'));
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::SEQUENCE);
        $option->setDoNotPersist();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $option->setLabel('Show title and rating before video starts');                                             //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);



        /**
         * FEED OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::ORDER_BY);
        $option->setDefaultValue(tubepress_api_const_options_values_OrderByValue::VIEW_COUNT);
        $option->setLabel('Order videos by');                                                                                                                                      //>(translatable)<
        $option->setDescription('Not all sort orders can be applied to all gallery types. See the <a href="http://tubepress.org/documentation">documentation</a> for more info.'); //>(translatable)<
        $option->setAcceptableValues(array(
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
        ));
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::PER_PAGE_SORT);
        $option->setDefaultValue(tubepress_api_const_options_values_PerPageSortValue::NONE);
        $option->setLabel('Per-page sort order');                                                       //>(translatable)<
        $option->setDescription('Additional sort order applied to each individual page of a gallery');  //>(translatable)<
        $option->setAcceptableValues(array(
            tubepress_api_const_options_values_PerPageSortValue::COMMENT_COUNT  => 'comment count',                 //>(translatable)<
            tubepress_api_const_options_values_PerPageSortValue::NEWEST         => 'date published (newest first)', //>(translatable)<
            tubepress_api_const_options_values_PerPageSortValue::OLDEST         => 'date published (oldest first)', //>(translatable)<
            tubepress_api_const_options_values_PerPageSortValue::DURATION       => 'length',                        //>(translatable)<
            tubepress_api_const_options_values_PerPageSortValue::NONE           => 'none',                          //>(translatable)<
            tubepress_api_const_options_values_PerPageSortValue::RANDOM         => 'random',                        //>(translatable)<
            tubepress_api_const_options_values_PerPageSortValue::RATING         => 'rating',                        //>(translatable)<
            tubepress_api_const_options_values_PerPageSortValue::TITLE          => 'title',                         //>(translatable)<
            tubepress_api_const_options_values_PerPageSortValue::VIEW_COUNT     => 'view count',                    //>(translatable)<
        ));
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $option->setDefaultValue(300);
        $option->setLabel('Maximum total videos to retrieve');                                                                   //>(translatable)<
        $option->setDescription('This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.'); //>(translatable)<
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
        $option->setLabel('Restrict search results to videos from author');                                    //>(translatable)<
        $option->setDescription('A YouTube or Vimeo user name. Only applies to search-based galleries.');      //>(translatable)<
        $option->setValidValueRegex('/\w*/');
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST);
        $option->setLabel('Video blacklist');                                            //>(translatable)<
        $option->setDescription('A list of video IDs that should never be displayed.');  //>(translatable)<
        $odr->registerOptionDescriptor($option);



        /**
         * INTERACTIVE SEARCH OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER);
        $option->setAcceptableValuesCallback(array('tubepress_plugins_core_Core', '_callbackGetValidVideoProviderNames'));
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL);
        $odr->registerOptionDescriptor($option);


        /**
         * META DISPLAY OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::AUTHOR);
        $option->setLabel('Author');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::CATEGORY);
        $option->setLabel('Category');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DESCRIPTION);
        $option->setLabel('Description');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::ID);
        $option->setLabel('ID');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::KEYWORDS);
        $option->setLabel('Keywords');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::LENGTH);
        $option->setLabel('Runtime');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::TITLE);
        $option->setLabel('Title');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::UPLOADED);
        $option->setLabel('Date posted');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::URL);
        $option->setLabel('URL');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::VIEWS);
        $option->setLabel('View count');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DATEFORMAT);
        $option->setDefaultValue('M j, Y');
        $option->setLabel('Date format');                                                                                                                    //>(translatable)<
        $option->setDescription('Set the textual formatting of date information for videos. See <a href="http://us.php.net/date">date</a> for examples.');   //>(translatable)<
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DESC_LIMIT);
        $option->setDefaultValue(80);
        $option->setLabel('Maximum description length');                                                                  //>(translatable)<
        $option->setDescription('Maximum number of characters to display in video descriptions. Set to 0 for no limit.'); //>(translatable)<
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::RELATIVE_DATES);
        $option->setDefaultValue(false);
        $option->setLabel('Use relative dates');                                    //>(translatable)<
        $option->setDescription('e.g. "yesterday" instead of "November 3, 1980".');  //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);


        /**
         * OPTIONS UI OPTIONS
         */
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS);
        $option->setLabel('Only show options applicable to...');    //>(translatable)<
        $odr->registerOptionDescriptor($option);


        /**
         * OUTPUT OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::GALLERY_SOURCE);
        $option->setDefaultValue('recently_featured');
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::OUTPUT);
        $option->setDoNotPersist();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::VIDEO);
        $option->setDoNotPersist();
        $odr->registerOptionDescriptor($option);



        /**
         * THUMBNAIL OPTIONS
         */

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION);
        $option->setLabel('<a href="http://wikipedia.org/wiki/Ajax_(programming)">Ajax</a>-enabled pagination'); //>(translatable)<
        $option->setDefaultValue(false);
        $option->setProOnly();
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Use "fluid" thumbnails');                                                         //>(translatable)<
        $option->setDescription('Dynamically set thumbnail spacing based on the width of their container.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::HQ_THUMBS);
        $option->setDefaultValue(false);
        $option->setLabel('Use high-quality thumbnails');                                                    //>(translatable)<
        $option->setDescription('Note: this option cannot be used with the "randomize thumbnails" feature.'); //>(translatable)<
        $option->setProOnly();
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination above thumbnails');                          //>(translatable)<
        $option->setDescription('Only applies to galleries that span multiple pages.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination below thumbnails');                          //>(translatable)<
        $option->setDescription('Only applies to galleries that span multiple pages.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Randomize thumbnail images');                                                                                                                                                                                                                                              //>(translatable)<
        $option->setDescription('Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);
        $option->setDefaultValue(20);
        $option->setLabel('Thumbnails per page');                     //>(translatable)<
        $option->setDescription('Default is 20. Maximum is 50.');     //>(translatable)<
        $option->setValidValueRegex($_regexPositiveInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THEME);
        $option->setLabel('Theme');                                                                                                                                               //>(translatable)<
        $option->setDescription('The TubePress theme to use for this gallery. Your themes can be found at <code>%s</code>, and default themes can be found at <code>%s</code>.'); //>(translatable)<
        $option->setAcceptableValuesCallback(array('tubepress_plugins_core_Core', '_callbackGetValidThemeOptions'));
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT);
        $option->setDefaultValue(90);
        $option->setLabel('Height (px) of thumbs');  //>(translatable)<
        $option->setDescription('Default is 90.');   //>(translatable)<
        $option->setValidValueRegex($_regexPositiveInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH);
        $option->setDefaultValue(120);
        $option->setLabel('Width (px) of thumbs');   //>(translatable)<
        $option->setDescription('Default is 120.');  //>(translatable)<
        $option->setValidValueRegex($_regexPositiveInteger);
        $odr->registerOptionDescriptor($option);
    }

    public static function _callbackGetValidPlayerImplementations()
    {
        $embeddedImpls = tubepress_impl_patterns_sl_ServiceLocator::getEmbeddedPlayers();
        $providerNames = self::_callbackGetValidVideoProviderNames();
        $detected      = array();

        foreach ($embeddedImpls as $embeddedImpl) {

            /**
             * If the embedded player service's name does not match a registered provider name,
             * it must be non provider based, so let's add it.
             */
            if (! in_array($embeddedImpl->getName(), $providerNames)) {

                $detected[$embeddedImpl->getName()] = $embeddedImpl->getFriendlyName();
            }
        }

        return array_merge(array(

            tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED => 'Provider default',  //>(translatable)<
        ), $detected);
    }

    public static function _callbackGetValidPlayerLocations()
    {
        $playerLocations = tubepress_impl_patterns_sl_ServiceLocator::getPlayerLocations();
        $toReturn        = array();

        foreach ($playerLocations as $playerLocation) {

            $toReturn[$playerLocation->getName()] = $playerLocation->getFriendlyName();
        }

        return $toReturn;
    }

    public static function _callbackGetValidVideoProviderNames()
    {
        return array_keys(self::_getValidProviderNamesToFriendlyNames());
    }

    public static function _callbackGetValidThemeOptions()
    {
        $environmentDetectorService     = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();
        $fileSystemService              = tubepress_impl_patterns_sl_ServiceLocator::getFileSystem();
        $fileSystemFinderFactoryService = tubepress_impl_patterns_sl_ServiceLocator::getFileSystemFinderFactory();

        $systemThemesDirectory = TUBEPRESS_ROOT . '/src/main/resources/default-themes';
        $userThemesDirectory   = $environmentDetectorService->getUserContentDirectory() . '/themes';

        $directoriesToSearch = array();

        if ($fileSystemService->exists($systemThemesDirectory)) {

            $directoriesToSearch[] = $systemThemesDirectory;
        }

        if ($fileSystemService->exists($userThemesDirectory)) {

            $directoriesToSearch[] = $userThemesDirectory;
        }

        $finder = $fileSystemFinderFactoryService->createFinder();

        $finder->directories()->in($directoriesToSearch)->depth(0);

        $themeNames = array();

        foreach ($finder as $themeDirectory) {

            /** @noinspection PhpUndefinedMethodInspection */
            $themeNames[] = basename($themeDirectory->getBasename());
        }

        sort($themeNames);

        $toReturn = array();

        foreach ($themeNames as $themeName) {

            $toReturn[$themeName] = $themeName;
        }

        return $toReturn;
    }

    private static function _getValidProviderNamesToFriendlyNames()
    {
        $videoProviders = tubepress_impl_patterns_sl_ServiceLocator::getVideoProviders();
        $toReturn       = array();

        foreach ($videoProviders as $videoProvider) {

            $toReturn[$videoProvider->getName()] = $videoProvider->getFriendlyName();
        }

        return $toReturn;
    }



    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /******* TUBEPRESS CONTENT DIRECTORY LOGIC *****************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/

    private static function _ensureTubePressContentDirectoryExists()
    {
        $ed = tubepress_impl_patterns_sl_ServiceLocator::getEnvironmentDetector();


        if ($ed->isWordPress()) {

            /* add the content directory if it's not already there */
            if (!is_dir(ABSPATH . 'wp-content/tubepress-content')) {

                self::_tryToMirror(
                    TUBEPRESS_ROOT . '/src/main/resources/user-content-skeleton/tubepress-content',
                    ABSPATH . 'wp-content');
            }

        } else {

            $basePath = TUBEPRESS_ROOT;

            /* add the content directory if it's not already there */
            if (!is_dir($basePath . '/tubepress-content')) {

                self::_tryToMirror(

                    $basePath . '/src/main/resources/user-content-skeleton/tubepress-content',
                    $basePath
                );
            }
        }
    }

    private static function _tryToMirror($source, $dest)
    {
        $fs = tubepress_impl_patterns_sl_ServiceLocator::getFileSystem();

        try {

            $fs->mirrorDirectoryPreventFileOverwrite($source, $dest);

        } catch (Exception $e) {

            //ignore
        }
    }


    /***********************************************************************************************************/
    /***********************************************************************************************************/
    /******* EVENT LISTENERS ***********************************************************************************/
    /***********************************************************************************************************/
    /***********************************************************************************************************/

    private static function _registerEventListeners()
    {
        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();
        $callback        = array('tubepress_plugins_core_Core', '_callbackHandleEvent');
        $eventNames      = array(

            tubepress_api_const_event_CoreEventNames::EMBEDDED_HTML_CONSTRUCTION,
            tubepress_api_const_event_CoreEventNames::EMBEDDED_TEMPLATE_CONSTRUCTION,
            tubepress_api_const_event_CoreEventNames::GALLERY_INIT_JS_CONSTRUCTION,
            tubepress_api_const_event_CoreEventNames::PLAYER_TEMPLATE_CONSTRUCTION,
            tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET,
            tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_TEMPLATE_CONSTRUCTION,
            tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION,
            tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION,
            tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION,
            tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT,
            tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
        );

        foreach ($eventNames as $eventName) {

            $eventDispatcher->addListener($eventName, $callback);
        }
    }

    public static function _callbackHandleEvent(tubepress_api_event_TubePressEvent $event)
    {
        switch ($event->getName()) {

            case tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_variablereadfromexternalinput_StringMagic', 'onIncomingInput'
                );

                break;

            case tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMeta', 'onSingleVideoTemplate'
                );

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_singlevideotemplate_CoreVariables', 'onSingleVideoTemplate'
                );

                break;

            case tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_TEMPLATE_CONSTRUCTION:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_searchinputtemplate_CoreVariables', 'onSearchInputTemplate'
                );

                break;

            case tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_videogallerypage_PerPageSorter', 'onVideoGalleryPage'
                );

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_videogallerypage_ResultCountCapper', 'onVideoGalleryPage'
                );

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_videogallerypage_VideoBlacklist', 'onVideoGalleryPage'
                );

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_videogallerypage_VideoPrepender', 'onVideoGalleryPage'
                );

                break;

            case tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_prevalidationoptionset_StringMagic', 'onPreValidationOptionSet'
                );

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover', 'onPreValidationOptionSet'
                );

                break;

            case tubepress_api_const_event_CoreEventNames::PLAYER_TEMPLATE_CONSTRUCTION:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_playertemplate_CoreVariables', 'onPlayerTemplate'
                );

                break;

            case tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_gallerytemplate_CoreVariables', 'onGalleryTemplate'
                );

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_gallerytemplate_EmbeddedPlayerName', 'onGalleryTemplate'
                );

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_gallerytemplate_Pagination', 'onGalleryTemplate'
                );

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_gallerytemplate_Player', 'onGalleryTemplate'
                );

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_gallerytemplate_VideoMeta', 'onGalleryTemplate'
                );

                break;

            case tubepress_api_const_event_CoreEventNames::GALLERY_INIT_JS_CONSTRUCTION:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_galleryinitjs_GalleryInitJsBaseParams', 'onGalleryInitJs'
                );

                break;

            case tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_galleryhtml_GalleryJs', 'onGalleryHtml'
                );

                break;

            case tubepress_api_const_event_CoreEventNames::EMBEDDED_TEMPLATE_CONSTRUCTION:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_embeddedtemplate_CoreVariables', 'onEmbeddedTemplate'
                );

                break;

            case tubepress_api_const_event_CoreEventNames::EMBEDDED_HTML_CONSTRUCTION:

                self::_call(

                    $event,
                    'tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi', 'onEmbeddedHtml'
                );
        }
    }

    private static function _call(tubepress_api_event_TubePressEvent $event, $serviceId, $functionName)
    {
        $serviceInstance = tubepress_impl_patterns_sl_ServiceLocator::getService($serviceId);

        $serviceInstance->$functionName($event);
    }

    /**
     * These are here to keep Pro strings translatable.
     * 'with FancyBox'    //>(translatable)<
     * 'with TinyBox'     //>(translatable)<
     * 'in a "detached" location (see the documentation)'  //>(translatable)<
     */
}

tubepress_plugins_core_Core::init();