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
class tubepress_plugins_core_CoreTest extends TubePressUnitTest
{
    private static $_regexPositiveInteger    = '/[1-9][0-9]{0,6}/';
    private static $_regexNonNegativeInteger = '/0|[1-9][0-9]{0,6}/';
    private static $_regexWordChars          = '/\w+/';

	private $_mockEventDispatcher;

    private $_mockOptionsDescriptorReference;

    private $_mockServiceCollectionsRegistry;

    private $_mockEnvironmentDetector;

    private $_mockFileSystem;

	function setup()
	{
		$this->_mockEventDispatcher = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        $this->_mockOptionsDescriptorReference = Mockery::mock(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockServiceCollectionsRegistry = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockServiceCollectionsRegistry);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionDescriptorReference($this->_mockOptionsDescriptorReference);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);

        if (!defined('ABSPATH')) {

            define('ABSPATH', '/value-of-abspath/');
        }

        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_mockFileSystem = Mockery::mock('ehough_fimble_api_Filesystem');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setFileSystem($this->_mockFileSystem);
	}

	function testCore()
    {
        $expected = array(

            array(tubepress_api_const_event_CoreEventNames::VARIABLE_READ_FROM_EXTERNAL_INPUT =>
                array('tubepress_plugins_core_impl_filters_variablereadfromexternalinput_StringMagic', 'onIncomingInput')),

            array(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_singlevideotemplate_VideoMeta', 'onSingleVideoTemplate')),

            array(tubepress_api_const_event_CoreEventNames::SINGLE_VIDEO_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_singlevideotemplate_CoreVariables', 'onSingleVideoTemplate')),

            array(tubepress_api_const_event_CoreEventNames::SEARCH_INPUT_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_searchinputtemplate_CoreVariables', 'onSearchInputTemplate')),

            array(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_videogallerypage_PerPageSorter', 'onVideoGalleryPage')),

            array(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_videogallerypage_ResultCountCapper', 'onVideoGalleryPage')),

            array(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_videogallerypage_VideoBlacklist', 'onVideoGalleryPage')),

            array(tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_videogallerypage_VideoPrepender', 'onVideoGalleryPage')),

            array(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET =>
                array('tubepress_plugins_core_impl_filters_prevalidationoptionset_StringMagic', 'onPreValidationOptionSet')),

            array(tubepress_api_const_event_CoreEventNames::PRE_VALIDATION_OPTION_SET =>
                array('tubepress_plugins_core_impl_filters_prevalidationoptionset_YouTubePlaylistPlPrefixRemover', 'onPreValidationOptionSet')),

            array(tubepress_api_const_event_CoreEventNames::PLAYER_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_playertemplate_CoreVariables', 'onPlayerTemplate')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_gallerytemplate_CoreVariables', 'onGalleryTemplate')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_gallerytemplate_EmbeddedPlayerName', 'onGalleryTemplate')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_gallerytemplate_Pagination', 'onGalleryTemplate')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_gallerytemplate_Player', 'onGalleryTemplate')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_gallerytemplate_VideoMeta', 'onGalleryTemplate')),

            array(tubepress_api_const_event_CoreEventNames::GALLERY_INIT_JS_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_galleryinitjs_GalleryInitJsBaseParams', 'onGalleryInitJs')),

            array(tubepress_api_const_event_CoreEventNames::THUMBNAIL_GALLERY_HTML_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_galleryhtml_GalleryJs', 'onGalleryHtml')),

            array(tubepress_api_const_event_CoreEventNames::EMBEDDED_TEMPLATE_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_embeddedtemplate_CoreVariables', 'onEmbeddedTemplate')),

            array(tubepress_api_const_event_CoreEventNames::EMBEDDED_HTML_CONSTRUCTION =>
                array('tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi', 'onEmbeddedHtml'))
        );

        $eventArray = array();

        foreach ($expected as $expect) {

            $eventName = array_keys($expect);
            $eventName = $eventName[0];

            if (! isset($eventArray[$eventName])) {

                $eventArray[$eventName] = array();
            }

            $eventArray[$eventName][] = $expect[$eventName];
        }

        foreach ($eventArray as $eventName => $callbacks) {

            $this->_mockEventDispatcher->shouldReceive('addListener')->times(count($callbacks))->with(

                $eventName, Mockery::on(function ($arr) use ($callbacks) {

                    foreach ($callbacks as $callback) {

                        if ($arr[0] instanceof $callback[0] && $arr[1] === $callback[1]) {

                            return true;
                        }
                    }

                    return false;
                }));
        }

        $this->_testOptions();

        $this->_mockServiceCollectionsRegistry->shouldReceive('registerService')->once()->with(

            tubepress_spi_http_PluggableAjaxCommandService::_,
            Mockery::on(function ($arg) {

                return $arg instanceof tubepress_plugins_core_impl_http_PlayerPluggableAjaxCommandService;
            })
        );

        $this->_mockServiceCollectionsRegistry->shouldReceive('registerService')->times(8)->with(

            tubepress_spi_player_PluggablePlayerLocationService::_,
            Mockery::on(function ($arg) {

                return $arg instanceof tubepress_plugins_core_impl_player_JqModalPluggablePlayerLocationService
                    || $arg instanceof tubepress_plugins_core_impl_player_NormalPluggablePlayerLocationService
                    || $arg instanceof tubepress_plugins_core_impl_player_PopupPluggablePlayerLocationService
                    || $arg instanceof tubepress_plugins_core_impl_player_ShadowboxPluggablePlayerLocationService
                    || $arg instanceof tubepress_plugins_core_impl_player_StaticPluggablePlayerLocationService
                    || $arg instanceof tubepress_plugins_core_impl_player_SoloPluggablePlayerLocationService
                    || $arg instanceof tubepress_plugins_core_impl_player_VimeoPluggablePlayerLocationService
                    || $arg instanceof tubepress_plugins_core_impl_player_YouTubePluggablePlayerLocationService;
            })
        );

        $this->_mockServiceCollectionsRegistry->shouldReceive('registerService')->times(5)->with(

            tubepress_spi_shortcode_PluggableShortcodeHandlerService::_,
            Mockery::on(function ($arg) {

                return $arg instanceof tubepress_plugins_core_impl_shortcode_SearchInputPluggableShortcodeHandlerService
                    || $arg instanceof tubepress_plugins_core_impl_shortcode_SearchOutputPluggableShortcodeHandlerService
                    || $arg instanceof tubepress_plugins_core_impl_shortcode_SingleVideoPluggableShortcodeHandlerService
                    || $arg instanceof tubepress_plugins_core_impl_shortcode_SoloPlayerPluggableShortcodeHandlerService
                    || $arg instanceof tubepress_plugins_core_impl_shortcode_ThumbGalleryPluggableShortcodeHandlerService;
            })
        );

        require __DIR__ . '/../../../../main/php/plugins/core/Core.php';

        $this->assertTrue(true);
    }

    private function _testOptions()
    {
        $mockPlayer = Mockery::mock(tubepress_spi_player_PluggablePlayerLocationService::_);
        $mockPlayers = array($mockPlayer);
        $mockPlayer->shouldReceive('getName')->times(10)->andReturn('abc');
        $mockPlayer->shouldReceive('getFriendlyName')->times(10)->andReturn('friendly name');
        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->times(10)->with(tubepress_spi_player_PluggablePlayerLocationService::_)->andReturn($mockPlayers);

        $mockEmbedded = Mockery::mock(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);
        $mockEmbedded->shouldReceive('getName')->times(112)->andReturn('yy-embed-name-yy');
        $mockEmbedded->shouldReceive('getFriendlyName')->times(56)->andReturn('friendly embed name');
        $mockEmbeds = array($mockEmbedded);
        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->times(56)->with(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_)->andReturn($mockEmbeds);

        $videoProvider = Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);
        $videoProvider->shouldReceive('getName')->times(81)->andReturn('xxvideo-provider-name-xx');
        $videoProvider->shouldReceive('getFriendlyName')->times(81)->andReturn('xx Friendly Provider Name xx');
        $videoProviders = array($videoProvider);
        $this->_mockServiceCollectionsRegistry->shouldReceive('getAllServicesOfType')->times(81)->with(tubepress_spi_provider_PluggableVideoProviderService::_)->andReturn($videoProviders);

        $this->setupMocks();

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $option->setDefaultValue(true);
        $option->setLabel('Enable debugging');                                                                                                                                                                                                                                                         //>(translatable)<
        $option->setDescription('If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.');  //>(translatable)<
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::HTTPS);
        $option->setDefaultValue(false);
        $option->setLabel('Enable HTTPS');                                                                //>(translatable)<
        $option->setDescription('Serve thumbnails and embedded video player over a secure connection.');  //>(translatable)<
        $option->setBoolean();
        $option->setProOnly();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::GALLERY_ID);
        $option->setValidValueRegex('/\w+/');
        $option->setDoNotPersist();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::KEYWORD);
        $option->setDefaultValue('tubepress');
        $option->setLabel('Shortcode keyword');                                                                                             //>(translatable)<
        $option->setDescription('The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexWordChars);
        $option->setCannotBeSetViaShortcode();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::HTTP_METHOD);
        $option->setDefaultValue(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET);
        $option->setLabel('HTTP method');                                                           //>(translatable)<
        $option->setDescription('Defines the HTTP method used in most TubePress Ajax operations');  //>(translatable)<
        $option->setAcceptableValues(array(

            ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET => ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET,
            ehough_shortstop_api_HttpRequest::HTTP_METHOD_POST => ehough_shortstop_api_HttpRequest::HTTP_METHOD_POST,
        ));
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR);
        $option->setDefaultValue(20);
        $option->setLabel('Cache cleaning factor');                                                                                             //>(translatable)<
        $option->setDescription('If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_DIR);
        $option->setLabel('Cache directory');                                                                                                                //>(translatable)<
        $option->setDescription('Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.'); //>(translatable)<
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_ENABLED);
        $option->setDefaultValue(false);
        $option->setLabel('Enable API cache');                                                                                                                    //>(translatable)<
        $option->setDescription('Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS);
        $option->setDefaultValue(3600);
        $option->setLabel('Cache expiration time (seconds)');                                                                                   //>(translatable)<
        $option->setDescription('Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).');   //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $option->setLabel('Play each video');                                                                                                 //>(translatable)<
        $option->setDefaultValue('normal');
        $option->setAcceptableValues(array('abc' => 'friendly name'));
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::AUTONEXT);
        $option->setLabel('Play videos sequentially without user intervention');  //>(translatable)<
        $option->setDescription('When a video finishes, this will start playing the next video in the gallery.');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $option->setProOnly();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $option->setLabel('Auto-play all videos');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);
        $option->setDefaultValue(350);
        $option->setLabel('Max height (px)');      //>(translatable)<
        $option->setDescription('Default is 350.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $option->setDefaultValue(425);
        $option->setLabel('Max width (px)');       //>(translatable)<
        $option->setDescription('Default is 425.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
        $option->setDefaultValue(true);
        $option->setLabel('Enable JavaScript API');       //>(translatable)<
        $option->setDescription('Allow TubePress to communicate with the embedded video player via JavaScript. This incurs a very small performance overhead, but is required for some features.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifyOption($option);



        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::LAZYPLAY);
        $option->setDefaultValue(true);
        $option->setLabel('"Lazy" play videos');                               //>(translatable)<
        $option->setDescription('Auto-play each video after thumbnail click.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::LOOP);
        $option->setDefaultValue(false);
        $option->setLabel('Loop');                                                     //>(translatable)<
        $option->setDescription('Continue playing the video until the user stops it.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::SEQUENCE);
        $option->setDoNotPersist();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $option->setLabel('Show title and rating before video starts');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST);
        $option->setLabel('Video blacklist');                                        //>(translatable)<
        $option->setDescription('A list of video IDs that should never be displayed.');  //>(translatable)<
        $this->_verifyOption($option);

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
        $this->_verifyOption($option);

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
        $this->_verifyOption($option);



        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $option->setDefaultValue(300);
        $option->setLabel('Maximum total videos to retrieve');                                                                   //>(translatable)<
        $option->setDescription('This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
        $option->setLabel('Restrict search results to videos from author');  //>(translatable)<
        $option->setDescription('A YouTube or Vimeo user name. Only applies to search-based galleries.');      //>(translatable)<
        $option->setValidValueRegex('/\w*/');
        $this->_verifyOption($option);



        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER);
        $option->setAcceptableValues(array('xxvideo-provider-name-xx'));
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_DOM_ID);
        $option->setProOnly();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DATEFORMAT);
        $option->setDefaultValue('M j, Y');
        $option->setLabel('Date format');                                                                                                                    //>(translatable)<
        $option->setDescription('Set the textual formatting of date information for videos. See <a href="http://us.php.net/date">date</a> for examples.');   //>(translatable)<
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DESC_LIMIT);
        $option->setDefaultValue(80);
        $option->setLabel('Maximum description length');                                                                  //>(translatable)<
        $option->setDescription('Maximum number of characters to display in video descriptions. Set to 0 for no limit.'); //>(translatable)<
        $option->setValidValueRegex(self::$_regexNonNegativeInteger);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::RELATIVE_DATES);
        $option->setDefaultValue(false);
        $option->setLabel('Use relative dates');                                    //>(translatable)<
        $option->setDescription('e.g. "yesterday" instead of "November 3, 1980".');  //>(translatable)<
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::AUTHOR);
        $option->setLabel('Author');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::CATEGORY);
        $option->setLabel('Category');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DESCRIPTION);
        $option->setLabel('Description');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::ID);
        $option->setLabel('ID');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::LENGTH);
        $option->setLabel('Runtime');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->_verifyOption($option);


        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::KEYWORDS);
        $option->setLabel('Keywords');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::TITLE);
        $option->setLabel('Title');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::UPLOADED);
        $option->setLabel('Date posted');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::URL);
        $option->setLabel('URL');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::VIEWS);
        $option->setLabel('View count');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::GALLERY_SOURCE);
        $option->setDefaultValue(tubepress_plugins_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::OUTPUT);
        $option->setDoNotPersist();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::VIDEO);
        $option->setDoNotPersist();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION);
        $option->setLabel('<a href="http://wikipedia.org/wiki/Ajax_(programming)">Ajax</a>-enabled pagination'); //>(translatable)<
        $option->setDefaultValue(false);
        $option->setProOnly();
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Use "fluid" thumbnails');                                                         //>(translatable)<
        $option->setDescription('Dynamically set thumbnail spacing based on the width of their container.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::HQ_THUMBS);
        $option->setDefaultValue(false);
        $option->setLabel('Use high-quality thumbnails');                                                    //>(translatable)<
        $option->setDescription('Note: this option cannot be used with the "randomize thumbnails" feature.'); //>(translatable)<
        $option->setProOnly();
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination above thumbnails');                         //>(translatable)<
        $option->setDescription('Only applies to galleries that span multiple pages.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination below thumbnails');                         //>(translatable)<
        $option->setDescription('Only applies to galleries that span multiple pages.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifyOption($option);



        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);
        $option->setDefaultValue(20);
        $option->setLabel('Thumbnails per page');                    //>(translatable)<
        $option->setDescription('Default is 20. Maximum is 50.');     //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THEME);
        $option->setLabel('Theme');                                                                                                                                       //>(translatable)<
        $option->setDescription('The TubePress theme to use for this gallery. Your themes can be found at <tt>%s</tt>, and default themes can be found at <tt>%s</tt>.'); //>(translatable)<
        $option->setAcceptableValues(array('xyz' => 'xyz'));
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT);
        $option->setDefaultValue(90);
        $option->setLabel('Height (px) of thumbs'); //>(translatable)<
        $option->setDescription('Default is 90.');   //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH);
        $option->setDefaultValue(120);
        $option->setLabel('Width (px) of thumbs');  //>(translatable)<
        $option->setDescription('Default is 120.');  //>(translatable)<
        $option->setValidValueRegex(self::$_regexPositiveInteger);
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Randomize thumbnail images');                                                                                                                                                                                                                                              //>(translatable)<
        $option->setDescription('Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_OptionsUi::PROVIDERS_TO_HIDE);
        $option->setLabel('Only show options applicable to...');    //>(translatable)<
        $this->_verifyOption($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
        $option->setDefaultValue(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);
        $option->setLabel('Implementation');                                                                                  //>(translatable)<
        $option->setDescription('The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).'); //>(translatable)<
        $option->setAcceptableValues(array('provider_based' => 'Provider default', 'yy-embed-name-yy' => 'friendly embed name'));
        $this->_verifyOption($option);

    }

    private function _verifyOption(tubepress_spi_options_OptionDescriptor $expectedOption)
    {
        $this->_mockOptionsDescriptorReference->shouldReceive('registerOptionDescriptor')->once()->with(Mockery::on(function ($registeredOption) use ($expectedOption) {

            return $registeredOption instanceof tubepress_spi_options_OptionDescriptor
                && $registeredOption->getAcceptableValues() === $expectedOption->getAcceptableValues()
                && $registeredOption->getAliases() === $expectedOption->getAliases()
                && $registeredOption->getDefaultValue() === $expectedOption->getDefaultValue()
                && $registeredOption->getDescription() === $expectedOption->getDescription()
                && $registeredOption->getLabel() === $expectedOption->getLabel()
                && $registeredOption->getName() === $expectedOption->getName()
                && $registeredOption->getValidValueRegex() === $expectedOption->getValidValueRegex()
                && $registeredOption->isAbleToBeSetViaShortcode() === $expectedOption->isAbleToBeSetViaShortcode()
                && $registeredOption->isBoolean() === $expectedOption->isBoolean()
                && $registeredOption->isMeantToBePersisted() === $expectedOption->isMeantToBePersisted()
                && $registeredOption->hasDiscreteAcceptableValues() === $expectedOption->hasDiscreteAcceptableValues()
                && $registeredOption->isProOnly() === $expectedOption->isProOnly();
        }));
    }

    private function setupMocks()
    {
        $environmentDetector = \Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);
        $filesystem          = \Mockery::mock('ehough_fimble_api_Filesystem');
        $finderFactory       = \Mockery::mock('ehough_fimble_api_FinderFactory');
        $finder              = \Mockery::mock('ehough_fimble_api_Finder');
        $fakeThemeDir        = \Mockery::mock();


        $environmentDetector->shouldReceive('getUserContentDirectory')->times(51)->andReturn('user-content-dir');

        $filesystem->shouldReceive('exists')->times(51)->with(TUBEPRESS_ROOT . '/src/main/resources/default-themes')->andReturn(false);
        $filesystem->shouldReceive('exists')->times(51)->with('user-content-dir/themes')->andReturn(true);

        $finderFactory->shouldReceive('createFinder')->times(51)->andReturn($finder);
        $finder->shouldReceive('directories')->times(51)->andReturn($finder);
        $finder->shouldReceive('in')->times(51)->with(array('user-content-dir/themes'))->andReturn($finder);
        $finder->shouldReceive('depth')->times(51)->with(0);

        $finder->shouldReceive('getIterator')->andReturn(new ArrayIterator(array($fakeThemeDir)));

        $fakeThemeDir->shouldReceive('getBasename')->times(51)->andReturn('xyz');

        $environmentDetector->shouldReceive('isWordPress')->once()->andReturn(true);

        $filesystem->shouldReceive('mirrorDirectoryPreventFileOverwrite')->once()->with(TUBEPRESS_ROOT . '/src/main/resources/user-content-skeleton/tubepress-content', '/value-of-abspath/wp-content');


        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($environmentDetector);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setFileSystem($filesystem);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setFileSystemFinderFactory($finderFactory);

    }
}