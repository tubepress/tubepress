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

/**
 * @covers tubepress_core_impl_options_CoreOptionProvider<extended>
 */
class tubepress_test_core_impl_options_CoreOptionProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_impl_options_CoreOptionProvider
     */
    private $_sut;

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
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockPlayerLocations;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockEmbeddedPlayers;

    /**
     * @var ehough_mockery_mockery_MockInterface[]
     */
    private $_mockVideoProviders;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeLibrary;

    public function onSetup()
    {
        $mockPlayerLocation = $this->mock(tubepress_core_api_player_PlayerLocationInterface::_);
        $this->_mockPlayerLocations = array($mockPlayerLocation);

        $mockEmbeddedPlayer = $this->mock(tubepress_core_api_embedded_EmbeddedProviderInterface::_);
        $this->_mockEmbeddedPlayers = array($mockEmbeddedPlayer);

        $mockVideoProvider = $this->mock(tubepress_core_api_provider_VideoProviderInterface::_);
        $this->_mockVideoProviders = array($mockVideoProvider);

        $this->_mockThemeLibrary = $this->mock(tubepress_core_api_theme_ThemeLibraryInterface::_);

        $this->_sut = new tubepress_core_impl_options_CoreOptionProvider($this->_mockThemeLibrary);

        $this->_sut->setPlayerLocations($this->_mockPlayerLocations);
        $this->_sut->setEmbeddedProviders($this->_mockEmbeddedPlayers);
        $this->_sut->setVideoProviders($this->_mockVideoProviders);
    }

    public function testGetThemeValues()
    {
        $this->_mockThemeLibrary->shouldReceive('getMapOfAllThemeNamesToTitles')->once()->andReturn(array(

            'xyz' => 'XZY Theme',
            'abc' => 'ABC Theme',
        ));

        $this->doTestGetDiscreteAcceptableValues(tubepress_core_api_const_options_Names::THEME, array(

            'abc' => 'ABC Theme',
            'xyz' => 'XZY Theme',
        ));
    }

    public function testGetValuesForVideoProviders()
    {
        $mockVideoProvider = $this->_mockVideoProviders[0];
        $mockVideoProvider->shouldReceive('getName')->once()->andReturn('provider name');
        $mockVideoProvider->shouldReceive('getFriendlyName')->once()->andReturn('provider friendly name');

        $this->doTestGetDiscreteAcceptableValues(tubepress_core_api_const_options_Names::SEARCH_PROVIDER, array(

            'provider name',
        ));
    }

    public function testGetValuesForEmbeddedPlayers()
    {
        $mockEmbeddedPlayer = $this->_mockEmbeddedPlayers[0];
        $mockEmbeddedPlayer->shouldReceive('getName')->twice()->andReturn('embedded name');
        $mockEmbeddedPlayer->shouldReceive('getFriendlyName')->once()->andReturn('player friendly name');

        $mockVideoProvider = $this->_mockVideoProviders[0];
        $mockVideoProvider->shouldReceive('getName')->once()->andReturn('provider name');
        $mockVideoProvider->shouldReceive('getFriendlyName')->once()->andReturn('provider friendly name');

        $this->doTestGetDiscreteAcceptableValues(tubepress_core_api_const_options_Names::PLAYER_IMPL, array(

            'provider_based' => 'Provider default',
            'embedded name' => 'player friendly name',
        ));
    }

    public function testGetValuesForPlayerLocation()
    {
        $mockPlayerLocation = $this->_mockPlayerLocations[0];
        $mockPlayerLocation->shouldReceive('getName')->once()->andReturn('player name');
        $mockPlayerLocation->shouldReceive('getFriendlyName')->once()->andReturn('player friendly name');

        $this->doTestGetDiscreteAcceptableValues(tubepress_core_api_const_options_Names::PLAYER_LOCATION, array(

            'player name' => 'player friendly name',
        ));
    }



    protected function prepare(tubepress_core_api_options_ProviderInterface $sut)
    {
        $this->_mockFileSystem                 = $this->mock('ehough_filesystem_FilesystemInterface');
        $this->_mockFinderFactory              = $this->mock('ehough_finder_FinderFactoryInterface');
        $this->_mockEnvironmentDetector        = $this->mock(tubepress_core_api_environment_EnvironmentInterface::_);

        if (!defined('ABSPATH')) {

            define('ABSPATH', '/value-of-abspath/');
        }

        $mockPlayer = $this->mock(tubepress_core_api_player_PlayerLocationInterface::_);
        $mockPlayer->shouldReceive('getName')->times(1)->andReturn('abc');
        $mockPlayer->shouldReceive('getFriendlyName')->times(1)->andReturn('friendly name');

        $mockEmbedded = $this->mock(tubepress_core_api_embedded_EmbeddedProviderInterface::_);
        $mockEmbedded->shouldReceive('getName')->times(2)->andReturn('yy-embed-name-yy');
        $mockEmbedded->shouldReceive('getFriendlyName')->times(1)->andReturn('friendly embed name');

        $videoProvider = $this->mock(tubepress_core_api_provider_VideoProviderInterface::_);
        $videoProvider->shouldReceive('getName')->times(2)->andReturn('xxvideo-provider-name-xx');
        $videoProvider->shouldReceive('getFriendlyName')->times(2)->andReturn('xx Friendly Provider Name xx');

        /**
         * @var $sut tubepress_core_impl_options_CoreOptionProvider
         */
        $sut->setPluggableEmbeddedPlayers(array($mockEmbedded));
        $sut->setPluggableVideoProviders(array($videoProvider));
        $sut->setPluggablePlayerLocations(array($mockPlayer));

        $this->_mockEnvironmentDetector->shouldReceive('getUserContentDirectory')->times(1)->andReturn('user-content-dir');

        $this->_mockFileSystem->shouldReceive('exists')->times(1)->with(TUBEPRESS_ROOT . '/src/main/web/themes')->andReturn(false);
        $this->_mockFileSystem->shouldReceive('exists')->times(1)->with('user-content-dir/themes')->andReturn(true);

        $fakeThemeDir        = $this->mock('sdf');
        $fakeThemeDir->shouldReceive('getBasename')->times(1)->andReturn('xyz');

        $finder = $this->mock('ehough_finder_FinderInterface');
        $finder->shouldReceive('directories')->times(1)->andReturn($finder);
        $finder->shouldReceive('in')->times(1)->with(array('user-content-dir/themes'))->andReturn($finder);
        $finder->shouldReceive('depth')->times(1)->with(0);
        $finder->shouldReceive('getIterator')->andReturn(new ArrayIterator(array($fakeThemeDir)));

        $this->_mockFinderFactory->shouldReceive('createFinder')->times(1)->andReturn($finder);
    }

    public function testGetMapOfOptionNamesToDefaultValues()
    {
        $expected = array(

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

        $actual = $this->_sut->getMapOfOptionNamesToDefaultValues();

        $this->assertEquals($expected, $actual);
    }

    public function testGetMapOfOptionNamesToUntranslatedLabels()
    {
        $expected = array(

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

        $actual = $this->_sut->getMapOfOptionNamesToUntranslatedLabels();

        $this->assertEquals($expected, $actual);
    }

    public function testGetMapOfOptionNamesToUntranslatedDescriptions()
    {
        $expected = array(

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

        $actual = $this->_sut->getMapOfOptionNamesToUntranslatedDescriptions();

        $this->assertEquals($expected, $actual);
    }

    /**
     * @return string[] An array, which may be empty but not null, of Pro option names from this provider.
     */
    public function testGetAllProOptionNames()
    {
        $expected = array(

            tubepress_core_api_const_options_Names::HTTPS,
            tubepress_core_api_const_options_Names::AUTONEXT,
            tubepress_core_api_const_options_Names::AJAX_PAGINATION,
            tubepress_core_api_const_options_Names::HQ_THUMBS,
        );

        $actual = $this->_sut->getAllProOptionNames();

        $this->assertEquals($expected, $actual);
    }

    public function testGetOptionsNamesThatShouldNotBePersisted()
    {
        $expected = array(

            tubepress_core_api_const_options_Names::GALLERY_ID,
            tubepress_core_api_const_options_Names::SEQUENCE,
            tubepress_core_api_const_options_Names::OUTPUT,
            tubepress_core_api_const_options_Names::VIDEO,
        );

        $actual = $this->_sut->getOptionsNamesThatShouldNotBePersisted();

        $this->assertEquals($expected, $actual);
    }

    public function testGetOptionNamesThatCannotBeSetViaShortcode()
    {
        $expected = array(

            tubepress_core_api_const_options_Names::KEYWORD
        );

        $actual = $this->_sut->getOptionNamesThatCannotBeSetViaShortcode();

        $this->assertEquals($expected, $actual);
    }

    protected function doTestGetDiscreteAcceptableValues($optionName, $expected)
    {
        $actual = $this->_sut->getDynamicDiscreteAcceptableValuesForOption($optionName);

        $this->assertEquals($expected, $actual, 'Wrong discrete values for ' . $optionName);
    }
}