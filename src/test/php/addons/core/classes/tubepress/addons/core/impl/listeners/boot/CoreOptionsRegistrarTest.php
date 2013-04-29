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
class tubepress_addons_core_impl_listeners_boot_CoreOptionsRegistrarTest extends tubepress_test_TubePressUnitTest
{

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockOptionsDescriptorReference;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFileSystem;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFinderFactory;

    /**
     * @var tubepress_addons_core_impl_listeners_boot_CoreOptionsRegistrar
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockOptionsDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockFileSystem                 = $this->createMockSingletonService('ehough_filesystem_FilesystemInterface');
        $this->_mockFinderFactory              = $this->createMockSingletonService('ehough_finder_FinderFactoryInterface');
        $this->_mockEnvironmentDetector        = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut = new tubepress_addons_core_impl_listeners_boot_CoreOptionsRegistrar();

        if (!defined('ABSPATH')) {

            define('ABSPATH', '/value-of-abspath/');
        }
    }

    public function testOptionsRegistration()
    {
        $_regexPositiveInteger    = '/[1-9][0-9]{0,6}/';
        $_regexNonNegativeInteger = '/0|[1-9][0-9]{0,6}/';
        $_regexWordChars          = '/\w+/';
        
        $mockPlayer = $this->createMockPluggableService(tubepress_spi_player_PluggablePlayerLocationService::_);
        $mockPlayer->shouldReceive('getName')->times(10)->andReturn('abc');
        $mockPlayer->shouldReceive('getFriendlyName')->times(10)->andReturn('friendly name');

        $mockEmbedded = $this->createMockPluggableService(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);
        $mockEmbedded->shouldReceive('getName')->times(110)->andReturn('yy-embed-name-yy');
        $mockEmbedded->shouldReceive('getFriendlyName')->times(55)->andReturn('friendly embed name');

        $videoProvider = $this->createMockPluggableService(tubepress_spi_provider_PluggableVideoProviderService::_);
        $videoProvider->shouldReceive('getName')->times(80)->andReturn('xxvideo-provider-name-xx');
        $videoProvider->shouldReceive('getFriendlyName')->times(80)->andReturn('xx Friendly Provider Name xx');

        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->times(50)->andReturn('user-content-dir');

        $this->_mockFileSystem->shouldReceive('exists')->times(50)->with(TUBEPRESS_ROOT . '/src/main/resources/default-themes')->andReturn(false);
        $this->_mockFileSystem->shouldReceive('exists')->times(50)->with('user-content-dir/themes')->andReturn(true);

        $fakeThemeDir        = ehough_mockery_Mockery::mock();
        $fakeThemeDir->shouldReceive('getBasename')->times(50)->andReturn('xyz');

        $finder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $finder->shouldReceive('directories')->times(50)->andReturn($finder);
        $finder->shouldReceive('in')->times(50)->with(array('user-content-dir/themes'))->andReturn($finder);
        $finder->shouldReceive('depth')->times(50)->with(0);
        $finder->shouldReceive('getIterator')->andReturn(new ArrayIterator(array($fakeThemeDir)));

        $this->_mockFinderFactory->shouldReceive('createFinder')->times(50)->andReturn($finder);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $option->setDefaultValue(true);
        $option->setLabel('Enable debugging');                                                                                                                                                                                                                                                         //>(translatable)<
        $option->setDescription('If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.');  //>(translatable)<
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::HTTPS);
        $option->setDefaultValue(false);
        $option->setLabel('Enable HTTPS');                                                                //>(translatable)<
        $option->setDescription('Serve thumbnails and embedded video player over a secure connection.');  //>(translatable)<
        $option->setBoolean();
        $option->setProOnly();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::GALLERY_ID);
        $option->setValidValueRegex('/\w+/');
        $option->setDoNotPersist();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::KEYWORD);
        $option->setDefaultValue('tubepress');
        $option->setLabel('Shortcode keyword');                                                                                             //>(translatable)<
        $option->setDescription('The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.'); //>(translatable)<
        $option->setValidValueRegex($_regexWordChars);
        $option->setCannotBeSetViaShortcode();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::HTTP_METHOD);
        $option->setDefaultValue(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET);
        $option->setLabel('HTTP method');                                                           //>(translatable)<
        $option->setDescription('Defines the HTTP method used in most TubePress Ajax operations');  //>(translatable)<
        $option->setAcceptableValues(array(

            ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET => ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET,
            ehough_shortstop_api_HttpRequest::HTTP_METHOD_POST => ehough_shortstop_api_HttpRequest::HTTP_METHOD_POST,
        ));
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR);
        $option->setDefaultValue(20);
        $option->setLabel('Cache cleaning factor');                                                                                             //>(translatable)<
        $option->setDescription('If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.'); //>(translatable)<
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_DIR);
        $option->setLabel('Cache directory');                                                                                                                //>(translatable)<
        $option->setDescription('Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.'); //>(translatable)<
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_ENABLED);
        $option->setDefaultValue(false);
        $option->setLabel('Enable API cache');                                                                                                                    //>(translatable)<
        $option->setDescription('Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS);
        $option->setDefaultValue(3600);
        $option->setLabel('Cache expiration time (seconds)');                                                                                   //>(translatable)<
        $option->setDescription('Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).');   //>(translatable)<
        $option->setValidValueRegex($_regexPositiveInteger);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $option->setLabel('Play each video');                                                                                                 //>(translatable)<
        $option->setDefaultValue('normal');
        $option->setAcceptableValues(array('abc' => 'friendly name'));
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::AUTONEXT);
        $option->setLabel('Play videos sequentially without user intervention');  //>(translatable)<
        $option->setDescription('When a video finishes, this will start playing the next video in the gallery.');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $option->setProOnly();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $option->setLabel('Auto-play all videos');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);
        $option->setDefaultValue(350);
        $option->setLabel('Max height (px)');      //>(translatable)<
        $option->setDescription('Default is 350.'); //>(translatable)<
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $option->setDefaultValue(425);
        $option->setLabel('Max width (px)');       //>(translatable)<
        $option->setDescription('Default is 425.'); //>(translatable)<
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
        $option->setDefaultValue(true);
        $option->setLabel('Enable JavaScript API');       //>(translatable)<
        $option->setDescription('Allow TubePress to communicate with the embedded video player via JavaScript. This incurs a very small performance overhead, but is required for some features.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::LAZYPLAY);
        $option->setDefaultValue(true);
        $option->setLabel('"Lazy" play videos');                               //>(translatable)<
        $option->setDescription('Auto-play each video after thumbnail click.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::LOOP);
        $option->setDefaultValue(false);
        $option->setLabel('Loop');                                                     //>(translatable)<
        $option->setDescription('Continue playing the video until the user stops it.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::SEQUENCE);
        $option->setDoNotPersist();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $option->setLabel('Show title and rating before video starts');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST);
        $option->setLabel('Video blacklist');                                        //>(translatable)<
        $option->setDescription('A list of video IDs that should never be displayed.');  //>(translatable)<
        $this->_verifySingleOptionRegistration($option);

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
        $this->_verifySingleOptionRegistration($option);

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
            tubepress_api_const_options_values_OrderByValue::REV_POSITION   => 'reversed position in a playlist', //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::RANDOM         => 'randomly',                 //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::RATING         => 'rating',                   //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::RELEVANCE      => 'relevance',                //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::TITLE          => 'title',                    //>(translatable)<
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT     => 'view count',               //>(translatable)<
        ));
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $option->setDefaultValue(300);
        $option->setLabel('Maximum total videos to retrieve');                                                                   //>(translatable)<
        $option->setDescription('This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.'); //>(translatable)<
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
        $option->setLabel('Restrict search results to videos from author');  //>(translatable)<
        $option->setDescription('A YouTube or Vimeo user name. Only applies to search-based galleries.');      //>(translatable)<
        $option->setValidValueRegex('/\w*/');
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER);
        $option->setAcceptableValues(array('xxvideo-provider-name-xx'));
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DATEFORMAT);
        $option->setDefaultValue('M j, Y');
        $option->setLabel('Date format');                                                                                                                    //>(translatable)<
        $option->setDescription('Set the textual formatting of date information for videos. See <a href="http://us.php.net/date">date</a> for examples.');   //>(translatable)<
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DESC_LIMIT);
        $option->setDefaultValue(80);
        $option->setLabel('Maximum description length');                                                                  //>(translatable)<
        $option->setDescription('Maximum number of characters to display in video descriptions. Set to 0 for no limit.'); //>(translatable)<
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::RELATIVE_DATES);
        $option->setDefaultValue(false);
        $option->setLabel('Use relative dates');                                    //>(translatable)<
        $option->setDescription('e.g. "yesterday" instead of "November 3, 1980".');  //>(translatable)<
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::AUTHOR);
        $option->setLabel('Author');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::CATEGORY);
        $option->setLabel('Category');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DESCRIPTION);
        $option->setLabel('Description');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::ID);
        $option->setLabel('ID');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::LENGTH);
        $option->setLabel('Runtime');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::KEYWORDS);
        $option->setLabel('Keywords');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::TITLE);
        $option->setLabel('Title');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::UPLOADED);
        $option->setLabel('Date posted');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::URL);
        $option->setLabel('URL');  //>(translatable)<
        $option->setDefaultValue(false);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::VIEWS);
        $option->setLabel('View count');  //>(translatable)<
        $option->setDefaultValue(true);
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::GALLERY_SOURCE);
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_FEATURED);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::OUTPUT);
        $option->setDoNotPersist();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::VIDEO);
        $option->setDoNotPersist();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION);
        $option->setLabel('<a href="http://wikipedia.org/wiki/Ajax_(programming)">Ajax</a>-enabled pagination'); //>(translatable)<
        $option->setDefaultValue(false);
        $option->setProOnly();
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Use "fluid" thumbnails');                                                         //>(translatable)<
        $option->setDescription('Dynamically set thumbnail spacing based on the width of their container.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::HQ_THUMBS);
        $option->setDefaultValue(false);
        $option->setLabel('Use high-quality thumbnails');                                                    //>(translatable)<
        $option->setDescription('Note: this option cannot be used with the "randomize thumbnails" feature.'); //>(translatable)<
        $option->setProOnly();
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination above thumbnails');                         //>(translatable)<
        $option->setDescription('Only applies to galleries that span multiple pages.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination below thumbnails');                         //>(translatable)<
        $option->setDescription('Only applies to galleries that span multiple pages.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);
        $option->setDefaultValue(20);
        $option->setLabel('Thumbnails per page');                    //>(translatable)<
        $option->setDescription('Default is 20. Maximum is 50.');     //>(translatable)<
        $option->setValidValueRegex($_regexPositiveInteger);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THEME);
        $option->setLabel('Theme');                                                                                                                                       //>(translatable)<
        $option->setDescription('The TubePress theme to use for this gallery. Your themes can be found at <code>%s</code>, and default themes can be found at <code>%s</code>.'); //>(translatable)<
        $option->setAcceptableValues(array('xyz' => 'xyz'));
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT);
        $option->setDefaultValue(90);
        $option->setLabel('Height (px) of thumbs'); //>(translatable)<
        $option->setDescription('Default is 90.');   //>(translatable)<
        $option->setValidValueRegex($_regexPositiveInteger);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH);
        $option->setDefaultValue(120);
        $option->setLabel('Width (px) of thumbs');  //>(translatable)<
        $option->setDescription('Default is 120.');  //>(translatable)<
        $option->setValidValueRegex($_regexPositiveInteger);
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Randomize thumbnail images');                                                                                                                                                                                                                                              //>(translatable)<
        $option->setDescription('Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.'); //>(translatable)<
        $option->setBoolean();
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS);
        $option->setLabel('Only show options applicable to...');    //>(translatable)<
        $this->_verifySingleOptionRegistration($option);

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
        $option->setDefaultValue(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);
        $option->setLabel('Implementation');                                                                                  //>(translatable)<
        $option->setDescription('The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).'); //>(translatable)<
        $option->setAcceptableValues(array('provider_based' => 'Provider default', 'yy-embed-name-yy' => 'friendly embed name'));
        $this->_verifySingleOptionRegistration($option);

        $this->_sut->onBootComplete(new ehough_tickertape_Event());

        $this->assertTrue(true);
    }

    private function _verifySingleOptionRegistration(tubepress_spi_options_OptionDescriptor $expectedOption)
    {
        $this->_mockOptionsDescriptorReference->shouldReceive('registerOptionDescriptor')->once()->with(ehough_mockery_Mockery::on(function ($registeredOption) use ($expectedOption) {

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
}