<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
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
 * Loads up the default options into TubePress.
 */
class tubepress_plugins_core_impl_listeners_CoreOptionsRegistrar
{
    private static $_regexPositiveInteger    = '/[1-9][0-9]{0,6}/';
    private static $_regexNonNegativeInteger = '/0|[1-9][0-9]{0,6}/';
    private static $_regexWordChars          = '/\w+/';

    public function onBoot(ehough_tickertape_api_Event $bootEvent)
    {
        $odr = tubepress_impl_patterns_ioc_KernelServiceLocator::getOptionDescriptorReference();
        
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $option->setDefaultValue(true);
        $option->setLabel('Enable debugging');                                                                                                                                                                                                                                                         //>(translatable)<
        $option->setDescription('If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.');  //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL);
        $option->setDefaultValue(false);
        $option->setLabel('Disable <a href="http://php.net/manual/en/function.curl-exec.php">cURL</a> HTTP transport');                    //>(translatable)<
        $option->setDescription('Do not attempt to use cURL to fetch remote feeds. Leave enabled unless you know what you are doing.');    //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP);
        $option->setDefaultValue(false);
        $option->setLabel('Disable <a href="http://php.net/http_request">HTTP extension</a> transport');                                                  //>(translatable)<
        $option->setDescription('Do not attempt to use the PHP HTTP extension to fetch remote feeds. Leave enabled unless you know what you are doing.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN);
        $option->setDefaultValue(false);
        $option->setLabel('Disable <a href="http://php.net/manual/en/function.fopen.php">fopen</a> HTTP transport');                     //>(translatable)<
        $option->setDescription('Do not attempt to use fopen to fetch remote feeds. Leave enabled unless you know what you are doing.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN);
        $option->setDefaultValue(false);
        $option->setLabel('Disable <a href="http://php.net/fsockopen">fsockopen</a> HTTP transport');                                        //>(translatable)<
        $option->setDescription('Do not attempt to use fsockopen to fetch remote feeds. Leave enabled unless you know what you are doing.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS);
        $option->setDefaultValue(false);
        $option->setLabel('Disable <a href="http://php.net/manual/en/intro.stream.php">PHP streams</a> HTTP transport');                        //>(translatable)<
        $option->setDescription('Do not attempt to use PHP streams to fetch remote feeds. Leave enabled unless you know what you are doing.');  //>(translatable)<
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
        $option->setValidValueRegex('/\w+/');
        $option->setDoNotPersist();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::KEYWORD);
        $option->setDefaultValue('tubepress');
        $option->setLabel('Shortcode keyword');                                                                                             //>(translatable)<
        $option->setDescription('The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $option->setCannotBeSetViaShortcode();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR);
        $option->setDefaultValue(20);
        $option->setLabel('Cache cleaning factor');                                                                                             //>(translatable)<
        $option->setDescription('If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
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
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $option->setLabel('Play each video');                                                                                              //>(translatable)<
        $option->setDefaultValue('normal');
//        $option->setAcceptableValues(array(
//            tubepress_api_const_options_values_PlayerLocationValue::NORMAL    => 'normally (at the top of your gallery)',                 //>(translatable)<
//            tubepress_api_const_options_values_PlayerLocationValue::POPUP     => 'in a popup window',                                     //>(translatable)<
//            tubepress_api_const_options_values_PlayerLocationValue::YOUTUBE   => 'from the video\'s original YouTube page',               //>(translatable)<
//            tubepress_api_const_options_values_PlayerLocationValue::VIMEO     => 'from the video\'s original Vimeo page',                 //>(translatable)<
//            tubepress_api_const_options_values_PlayerLocationValue::SHADOWBOX => 'with Shadowbox',                                        //>(translatable)<
//            tubepress_api_const_options_values_PlayerLocationValue::JQMODAL   => 'with jqModal',                                          //>(translatable)<
//            tubepress_api_const_options_values_PlayerLocationValue::TINYBOX   => 'with TinyBox',                                          //>(translatable)<
//            tubepress_api_const_options_values_PlayerLocationValue::FANCYBOX  => 'with FancyBox',                                         //>(translatable)<
//            tubepress_api_const_options_values_PlayerLocationValue::STATICC   => 'statically (page refreshes on each thumbnail click)',   //>(translatable)<
//            tubepress_api_const_options_values_PlayerLocationValue::SOLO      => 'in a new window on its own',                            //>(translatable)<
//            tubepress_api_const_options_values_PlayerLocationValue::DETACHED  => 'in a "detached" location (see the documentation)'		  //>(translatable)<
//        ));
        $odr->registerOptionDescriptor($option);



        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::AUTONEXT);
        $option->setLabel('Play videos sequentially without user intervention');  //>(translatable)<
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
        $option->setLabel('Max height (px)');      //>(translatable)<
        $option->setDescription('Default is 350.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $option->setDefaultValue(425);
        $option->setLabel('Max width (px)');       //>(translatable)<
        $option->setDescription('Default is 425.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
        $option->setDefaultValue(true);
        $option->setLabel('Enable JavaScript API');       //>(translatable)<
        $option->setDescription('Allow TubePress to communicate with the embedded video player via JavaScript. This incurs a very small performance overhead, but is required for some features.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::LAZYPLAY);
        $option->setDefaultValue(true);
        $option->setLabel('"Lazy" play videos');                               //>(translatable)<
        $option->setDescription('Auto-play each video after thumbnail click.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::LOOP);
        $option->setDefaultValue(false);
        $option->setLabel('Loop');                                                     //>(translatable)<
        $option->setDescription('Continue playing the video until the user stops it.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::SEQUENCE);
        $option->setDoNotPersist();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $option->setLabel('Show title and rating before video starts');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST);
        $option->setLabel('Video blacklist');                                        //>(translatable)<
        $option->setDescription('A list of video IDs that should never be displayed.');  //>(translatable)<
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::PER_PAGE_SORT);
        $option->setDefaultValue(tubepress_api_const_options_values_PerPageSortValue::NONE);
        $option->setLabel('Per-page sort order');                                            //>(translatable)<
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

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::ORDER_BY);
        $option->setDefaultValue(tubepress_api_const_options_values_OrderByValue::VIEW_COUNT);
        $option->setLabel('Order videos by');                                                                                                                                      //>(translatable)<
        $option->setDescription('Not all sort orders can be applied to all gallery types. See the <a href="http://tubepress.org/documentation">documentation</a> for more info.'); //>(translatable)<
        $option->setAcceptableValues(array(
            tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT  => 'comment count',            //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::NEWEST         => 'date published (newest first)',                   //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::OLDEST         => 'date published (oldest first)',                   //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::DURATION       => 'length',                   //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::POSITION       => 'position in a playlist',   //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::RANDOM         => 'randomly',                 //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::RATING         => 'rating',                   //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::RELEVANCE      => 'relevance',                //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::TITLE          => 'title',                    //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT     => 'view count',               //>(translatable)<
        ));
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $option->setDefaultValue(300);
        $option->setLabel('Maximum total videos to retrieve');                                                                   //>(translatable)<
        $option->setDescription('This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
        $option->setLabel('Restrict search results to videos from author');  //>(translatable)<
        $option->setDescription('A YouTube or Vimeo user name. Only applies to search-based galleries.');      //>(translatable)<
        $option->setValidValueRegex('/\w*/');
        $odr->registerOptionDescriptor($option);






        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER);
        $option->setAcceptableValues(array(
            'youtube',
            'vimeo',
        ));
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_DOM_ID);
        $option->setProOnly();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL);
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
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::RELATIVE_DATES);
        $option->setDefaultValue(false);
        $option->setLabel('Use relative dates');                                    //>(translatable)<
        $option->setDescription('e.g. "yesterday" instead of "November 3, 1980".');  //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

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

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::LENGTH);
        $option->setLabel('Runtime');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);






        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::KEYWORDS);
        $option->setLabel('Keywords');  //>(translatable)<
        $option->setDefaultValue(false);
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

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::GALLERY_SOURCE);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED);
//        $option->setAcceptableValues(array(
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES,
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED,
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_DISCUSSED,
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RECENT,
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_RESPONDED,
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST,
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR,
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH,
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_FAVORITES,
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_TOP_RATED,
//            tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_USER,
//            tubepress_api_const_options_values_GallerySourceValue::VIMEO_ALBUM,
//            tubepress_api_const_options_values_GallerySourceValue::VIMEO_APPEARS_IN,
//            tubepress_api_const_options_values_GallerySourceValue::VIMEO_CHANNEL,
//            tubepress_api_const_options_values_GallerySourceValue::VIMEO_CREDITED,
//            tubepress_api_const_options_values_GallerySourceValue::VIMEO_GROUP,
//            tubepress_api_const_options_values_GallerySourceValue::VIMEO_LIKES,
//            tubepress_api_const_options_values_GallerySourceValue::VIMEO_SEARCH,
//            tubepress_api_const_options_values_GallerySourceValue::VIMEO_UPLOADEDBY,
//        ));
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::OUTPUT);
        $option->setDoNotPersist();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::VIDEO);
        $option->setDoNotPersist();
        $odr->registerOptionDescriptor($option);

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
        $option->setLabel('Show pagination above thumbnails');                         //>(translatable)<
        $option->setDescription('Only applies to galleries that span multiple pages.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination below thumbnails');                         //>(translatable)<
        $option->setDescription('Only applies to galleries that span multiple pages.'); //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);



        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);
        $option->setDefaultValue(20);
        $option->setLabel('Thumbnails per page');                    //>(translatable)<
        $option->setDescription('Default is 20. Maximum is 50.');     //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THEME);
        $option->setLabel('Theme');                                                                                                                                       //>(translatable)<
        $option->setDescription('The TubePress theme to use for this gallery. Your themes can be found at <tt>%s</tt>, and default themes can be found at <tt>%s</tt>.'); //>(translatable)<
        $option->setAcceptableValues($this->_getThemeValues());
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT);
        $option->setDefaultValue(90);
        $option->setLabel('Height (px) of thumbs'); //>(translatable)<
        $option->setDescription('Default is 90.');   //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH);
        $option->setDefaultValue(120);
        $option->setLabel('Width (px) of thumbs');  //>(translatable)<
        $option->setDescription('Default is 120.');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_OptionsUi::SHOW_VIMEO_OPTIONS);
        $option->setDefaultValue(true);
        $option->setLabel('Vimeo');    //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_OptionsUi::SHOW_YOUTUBE_OPTIONS);
        $option->setDefaultValue(true);
        $option->setLabel('YouTube');    //>(translatable)<
        $option->setBoolean();
        $odr->registerOptionDescriptor($option);
    }

    private function _getThemeValues()
    {
        $environmentDetectorService     = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();
        $fileSystemService              = tubepress_impl_patterns_ioc_KernelServiceLocator::getFileSystem();
        $fileSystemFinderFactoryService = tubepress_impl_patterns_ioc_KernelServiceLocator::getFileSystemFinderFactory();

        $systemThemesDirectory =
            $environmentDetectorService->getTubePressBaseInstallationPath() . '/src/main/resources/default-themes';

        $userThemesDirectory =
            $environmentDetectorService->getUserContentDirectory() . '/themes';

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
}
