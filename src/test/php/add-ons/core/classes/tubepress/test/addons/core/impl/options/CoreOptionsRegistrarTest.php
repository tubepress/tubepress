<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_core_impl_options_CoreOptionsProvider<extended>
 */
class tubepress_test_addons_core_impl_options_CoreOptionsProviderTest extends tubepress_test_impl_options_AbstractOptionDescriptorProviderTest
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

    protected function prepare(tubepress_spi_options_PluggableOptionDescriptorProvider $sut)
    {
        $this->_mockOptionsDescriptorReference = $this->createMockSingletonService(tubepress_spi_options_OptionDescriptorReference::_);
        $this->_mockFileSystem                 = $this->createMockSingletonService('ehough_filesystem_FilesystemInterface');
        $this->_mockFinderFactory              = $this->createMockSingletonService('ehough_finder_FinderFactoryInterface');
        $this->_mockEnvironmentDetector        = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        if (!defined('ABSPATH')) {

            define('ABSPATH', '/value-of-abspath/');
        }

        $mockPlayer = ehough_mockery_Mockery::mock(tubepress_spi_player_PluggablePlayerLocationService::_);
        $mockPlayer->shouldReceive('getName')->times(1)->andReturn('abc');
        $mockPlayer->shouldReceive('getFriendlyName')->times(1)->andReturn('friendly name');

        $mockEmbedded = ehough_mockery_Mockery::mock(tubepress_spi_embedded_PluggableEmbeddedPlayerService::_);
        $mockEmbedded->shouldReceive('getName')->times(2)->andReturn('yy-embed-name-yy');
        $mockEmbedded->shouldReceive('getFriendlyName')->times(1)->andReturn('friendly embed name');

        $videoProvider = ehough_mockery_Mockery::mock(tubepress_spi_provider_PluggableVideoProviderService::_);
        $videoProvider->shouldReceive('getName')->times(2)->andReturn('xxvideo-provider-name-xx');
        $videoProvider->shouldReceive('getFriendlyName')->times(2)->andReturn('xx Friendly Provider Name xx');

        /**
         * @var $sut tubepress_addons_core_impl_options_CoreOptionsProvider
         */
        $sut->setPluggableEmbeddedPlayers(array($mockEmbedded));
        $sut->setPluggableVideoProviders(array($videoProvider));
        $sut->setPluggablePlayerLocations(array($mockPlayer));

        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->times(1)->andReturn('user-content-dir');

        $this->_mockFileSystem->shouldReceive('exists')->times(1)->with(TUBEPRESS_ROOT . '/src/main/resources/default-themes')->andReturn(false);
        $this->_mockFileSystem->shouldReceive('exists')->times(1)->with('user-content-dir/themes')->andReturn(true);

        $fakeThemeDir        = ehough_mockery_Mockery::mock();
        $fakeThemeDir->shouldReceive('getBasename')->times(1)->andReturn('xyz');

        $finder = ehough_mockery_Mockery::mock('ehough_finder_FinderInterface');
        $finder->shouldReceive('directories')->times(1)->andReturn($finder);
        $finder->shouldReceive('in')->times(1)->with(array('user-content-dir/themes'))->andReturn($finder);
        $finder->shouldReceive('depth')->times(1)->with(0);
        $finder->shouldReceive('getIterator')->andReturn(new ArrayIterator(array($fakeThemeDir)));

        $this->_mockFinderFactory->shouldReceive('createFinder')->times(1)->andReturn($finder);
    }

    protected function buildSut()
    {
        return new tubepress_addons_core_impl_options_CoreOptionsProvider();
    }

    protected function getExpectedOptions()
    {
        $_regexPositiveInteger    = '/[1-9][0-9]{0,6}/';
        $_regexNonNegativeInteger = '/0|[1-9][0-9]{0,6}/';
        $_regexWordChars          = '/\w+/';

        $expected = array();
        
        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::DEBUG_ON);
        $option->setDefaultValue(true);
        $option->setLabel('Enable debugging');
        $option->setDescription('If checked, anyone will be able to view your debugging information. This is a rather small privacy risk. If you\'re not having problems with TubePress, or you\'re worried about revealing any details of your TubePress pages, feel free to disable the feature.');
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::HTTPS);
        $option->setDefaultValue(false);
        $option->setLabel('Enable HTTPS');
        $option->setDescription('Serve thumbnails and embedded video player over a secure connection.');
        $option->setBoolean();
        $option->setProOnly();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::GALLERY_ID);
        $option->setValidValueRegex('/\w+/');
        $option->setDoNotPersist();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::KEYWORD);
        $option->setDefaultValue('tubepress');
        $option->setLabel('Shortcode keyword');
        $option->setDescription('The word you insert (in plaintext, between square brackets) into your posts/pages to display a gallery.');
        $option->setValidValueRegex($_regexWordChars);
        $option->setCannotBeSetViaShortcode();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Advanced::HTTP_METHOD);
        $option->setDefaultValue(ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET);
        $option->setLabel('HTTP method');
        $option->setDescription('Defines the HTTP method used in most TubePress Ajax operations');
        $option->setAcceptableValues(array(

            ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET => ehough_shortstop_api_HttpRequest::HTTP_METHOD_GET,
            ehough_shortstop_api_HttpRequest::HTTP_METHOD_POST => ehough_shortstop_api_HttpRequest::HTTP_METHOD_POST,
        ));
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_CLEAN_FACTOR);
        $option->setDefaultValue(20);
        $option->setLabel('Cache cleaning factor');
        $option->setDescription('If you enter X, the entire cache will be cleaned every 1/X cache writes. Enter 0 to disable cache cleaning.');
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_DIR);
        $option->setLabel('Cache directory');
        $option->setDescription('Leave blank to attempt to use your system\'s temp directory. Otherwise enter the absolute path of a writeable directory.');
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_ENABLED);
        $option->setDefaultValue(false);
        $option->setLabel('Enable API cache');
        $option->setDescription('Store API responses in a cache file to significantly reduce load times for your galleries at the slight expense of freshness.');
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Cache::CACHE_LIFETIME_SECONDS);
        $option->setDefaultValue(3600);
        $option->setLabel('Cache expiration time (seconds)');
        $option->setDescription('Cache entries will be considered stale after the specified number of seconds. Default is 3600 (one hour).');
        $option->setValidValueRegex($_regexPositiveInteger);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::PLAYER_LOCATION);
        $option->setLabel('Play each video');
        $option->setDefaultValue('normal');
        $option->setAcceptableValues(array('abc' => 'friendly name'));
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::AUTONEXT);
        $option->setLabel('Play videos sequentially without user intervention');
        $option->setDescription('When a video finishes, this will start playing the next video in the gallery.');
        $option->setDefaultValue(true);
        $option->setBoolean();
        $option->setProOnly();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::AUTOPLAY);
        $option->setLabel('Auto-play all videos');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT);
        $option->setDefaultValue(350);
        $option->setLabel('Max height (px)');
        $option->setDescription('Default is 350.');
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH);
        $option->setDefaultValue(425);
        $option->setLabel('Max width (px)');
        $option->setDescription('Default is 425.');
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::ENABLE_JS_API);
        $option->setDefaultValue(true);
        $option->setLabel('Enable JavaScript API');
        $option->setDescription('Allow TubePress to communicate with the embedded video player via JavaScript. This incurs a very small performance overhead, but is required for some features.');
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::LAZYPLAY);
        $option->setDefaultValue(true);
        $option->setLabel('"Lazy" play videos');
        $option->setDescription('Auto-play each video after thumbnail click.');
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::LOOP);
        $option->setDefaultValue(false);
        $option->setLabel('Loop');
        $option->setDescription('Continue playing the video until the user stops it.');
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::SEQUENCE);
        $option->setDoNotPersist();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::SHOW_INFO);
        $option->setLabel('Show title and rating before video starts');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::VIDEO_BLACKLIST);
        $option->setLabel('Video blacklist');
        $option->setDescription('A list of video IDs that should never be displayed.');
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::PER_PAGE_SORT);
        $option->setDefaultValue(tubepress_api_const_options_values_PerPageSortValue::NONE);
        $option->setLabel('Per-page sort order');
        $option->setDescription('Additional sort order applied to each individual page of a gallery');
        $option->setAcceptableValues(array(
            tubepress_api_const_options_values_PerPageSortValue::COMMENT_COUNT  => 'comment count',
            tubepress_api_const_options_values_PerPageSortValue::NEWEST         => 'date published (newest first)',
            tubepress_api_const_options_values_PerPageSortValue::OLDEST         => 'date published (oldest first)',
            tubepress_api_const_options_values_PerPageSortValue::DURATION       => 'length',
            tubepress_api_const_options_values_PerPageSortValue::NONE           => 'none',
            tubepress_api_const_options_values_PerPageSortValue::RANDOM         => 'random',
            tubepress_api_const_options_values_PerPageSortValue::RATING         => 'rating',
            tubepress_api_const_options_values_PerPageSortValue::TITLE          => 'title',
            tubepress_api_const_options_values_PerPageSortValue::VIEW_COUNT     => 'view count',
        ));
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::ORDER_BY);
        $option->setDefaultValue(tubepress_api_const_options_values_OrderByValue::DEFAULTT);
        $option->setLabel('Order videos by');
        $option->setDescription('Not all sort orders can be applied to all gallery types. See the <a href="http://tubepress.com/documentation" target="_blank">documentation</a> for more info.');
        $option->setAcceptableValues(array(
            tubepress_api_const_options_values_OrderByValue::DEFAULTT       => 'default',
            tubepress_api_const_options_values_OrderByValue::COMMENT_COUNT  => 'comment count',
            tubepress_api_const_options_values_OrderByValue::NEWEST         => 'date published (newest first)',
            tubepress_api_const_options_values_OrderByValue::OLDEST         => 'date published (oldest first)',
            tubepress_api_const_options_values_OrderByValue::DURATION       => 'length',
            tubepress_api_const_options_values_OrderByValue::POSITION       => 'position in a playlist',
            tubepress_api_const_options_values_OrderByValue::REV_POSITION   => 'reversed position in a playlist',
            tubepress_api_const_options_values_OrderByValue::RANDOM         => 'randomly',
            tubepress_api_const_options_values_OrderByValue::RATING         => 'rating',
            tubepress_api_const_options_values_OrderByValue::RELEVANCE      => 'relevance',
            tubepress_api_const_options_values_OrderByValue::TITLE          => 'title',
            tubepress_api_const_options_values_OrderByValue::VIEW_COUNT     => 'view count',
        ));
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $option->setDefaultValue(300);
        $option->setLabel('Maximum total videos to retrieve');
        $option->setDescription('This can help to reduce the number of pages in your gallery. Set to "0" to remove any limit.');
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER);
        $option->setLabel('Restrict search results to videos from author');
        $option->setDescription('A YouTube or Vimeo user name. Only applies to search-based galleries.');
        $option->setValidValueRegex('/\w*/');
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_PROVIDER);
        $option->setAcceptableValues(array('xxvideo-provider-name-xx'));
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_ONLY);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_InteractiveSearch::SEARCH_RESULTS_URL);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DATEFORMAT);
        $option->setDefaultValue('M j, Y');
        $option->setLabel('Date format');
        $option->setDescription('Set the textual formatting of date information for videos. See <a href="http://us.php.net/date" target="_blank">date</a> for examples.');
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DESC_LIMIT);
        $option->setDefaultValue(80);
        $option->setLabel('Maximum description length');
        $option->setDescription('Maximum number of characters to display in video descriptions. Set to 0 for no limit.');
        $option->setValidValueRegex($_regexNonNegativeInteger);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::RELATIVE_DATES);
        $option->setDefaultValue(false);
        $option->setLabel('Use relative dates');
        $option->setDescription('e.g. "yesterday" instead of "November 3, 1980".');
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::AUTHOR);
        $option->setLabel('Author');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::CATEGORY);
        $option->setLabel('Category');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::DESCRIPTION);
        $option->setLabel('Description');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::ID);
        $option->setLabel('ID');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::LENGTH);
        $option->setLabel('Runtime');
        $option->setDefaultValue(true);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::KEYWORDS);
        $option->setLabel('Keywords');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::TITLE);
        $option->setLabel('Title');
        $option->setDefaultValue(true);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::UPLOADED);
        $option->setLabel('Date posted');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::URL);
        $option->setLabel('URL');
        $option->setDefaultValue(false);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Meta::VIEWS);
        $option->setLabel('View count');
        $option->setDefaultValue(true);
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::GALLERY_SOURCE);
        $option->setDefaultValue(tubepress_addons_youtube_api_const_options_values_GallerySourceValue::YOUTUBE_MOST_POPULAR);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::OUTPUT);
        $option->setDoNotPersist();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Output::VIDEO);
        $option->setDoNotPersist();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION);
        $option->setLabel('<a href="http://wikipedia.org/wiki/Ajax_(programming)" target="_blank">Ajax</a>-enabled pagination');
        $option->setDefaultValue(false);
        $option->setProOnly();
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::FLUID_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Use "fluid" thumbnails');
        $option->setDescription('Dynamically set thumbnail spacing based on the width of their container.');
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::HQ_THUMBS);
        $option->setDefaultValue(false);
        $option->setLabel('Use high-quality thumbnails');
        $option->setDescription('Note: this option cannot be used with the "randomize thumbnails" feature.');
        $option->setProOnly();
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination above thumbnails');
        $option->setDescription('Only applies to galleries that span multiple pages.');
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW);
        $option->setDefaultValue(true);
        $option->setLabel('Show pagination below thumbnails');
        $option->setDescription('Only applies to galleries that span multiple pages.');
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE);
        $option->setDefaultValue(20);
        $option->setLabel('Thumbnails per page');
        $option->setDescription('Default is 20. Maximum is 50.');
        $option->setValidValueRegex($_regexPositiveInteger);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THEME);
        $option->setLabel('Theme');
        $option->setDescription('The TubePress theme to use for this gallery. Your themes can be found at <code>%s</code>, and default themes can be found at <code>%s</code>.');
        $option->setAcceptableValues(array('xyz' => 'xyz'));
        $option->setDefaultValue('default');
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THUMB_HEIGHT);
        $option->setDefaultValue(90);
        $option->setLabel('Height (px) of thumbs');
        $option->setDescription('Default is 90.');
        $option->setValidValueRegex($_regexPositiveInteger);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::THUMB_WIDTH);
        $option->setDefaultValue(120);
        $option->setLabel('Width (px) of thumbs');
        $option->setDescription('Default is 120.');
        $option->setValidValueRegex($_regexPositiveInteger);
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Thumbs::RANDOM_THUMBS);
        $option->setDefaultValue(true);
        $option->setLabel('Randomize thumbnail images');
        $option->setDescription('Most videos come with several thumbnails. By selecting this option, each time someone views your gallery they will see the same videos with each video\'s thumbnail randomized. Note: this option cannot be used with the "high quality thumbnails" feature.');
        $option->setBoolean();
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_OptionsUi::DISABLED_OPTIONS_PAGE_PARTICIPANTS);
        $option->setLabel('Only show options applicable to...');
        $expected[] = $option;

        $option = new tubepress_spi_options_OptionDescriptor(tubepress_api_const_options_names_Embedded::PLAYER_IMPL);
        $option->setDefaultValue(tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);
        $option->setLabel('Implementation');
        $option->setDescription('The brand of the embedded player. Default is the provider\'s player (YouTube, Vimeo, etc).');
        $option->setAcceptableValues(array('provider_based' => 'Provider default', 'yy-embed-name-yy' => 'friendly embed name'));
        $expected[] = $option;

        return $expected;
    }
}