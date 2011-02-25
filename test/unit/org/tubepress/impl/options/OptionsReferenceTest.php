<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/options/OptionsReference.class.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/api/const/options/CategoryName.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_impl_options_OptionsReferenceTest extends TubePressUnitTest {
    
    private $_options = array(
        org_tubepress_api_const_options_Type::COLOR => array(
            org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR     => '999999',
            org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT => 'FFFFFF'
        ),
        org_tubepress_api_const_options_Type::MODE  => array(
            org_tubepress_api_const_options_names_Output::MODE => org_tubepress_api_const_options_values_ModeValue::FEATURED
        ),
        org_tubepress_api_const_options_Type::TEXT => array(
            org_tubepress_api_const_options_names_Advanced::DATEFORMAT           => 'M j, Y',
            org_tubepress_api_const_options_names_Advanced::KEYWORD              => 'tubepress',
            org_tubepress_api_const_options_names_Advanced::VIDEO_BLACKLIST      => '',
            org_tubepress_api_const_options_names_Output::FAVORITES_VALUE        => 'mrdeathgod',
            org_tubepress_api_const_options_names_Output::PLAYLIST_VALUE         => 'D2B04665B213AE35',
            org_tubepress_api_const_options_names_Output::TAG_VALUE              => 'stewart daily show',
            org_tubepress_api_const_options_names_Output::USER_VALUE             => '3hough',
            org_tubepress_api_const_options_names_Feed::DEV_KEY                  => 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg',
            org_tubepress_api_const_options_names_Feed::VIMEO_KEY                => '',
            org_tubepress_api_const_options_names_Feed::VIMEO_SECRET             => '',
            org_tubepress_api_const_options_names_Widget::TITLE                  => 'TubePress',
            org_tubepress_api_const_options_names_Widget::TAGSTRING              => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']',
            org_tubepress_api_const_options_names_Output::VIDEO                  => '',
            org_tubepress_api_const_options_names_Output::VIMEO_UPLOADEDBY_VALUE => 'mattkaar',
            org_tubepress_api_const_options_names_Output::VIMEO_LIKES_VALUE      => 'coiffier',
            org_tubepress_api_const_options_names_Output::VIMEO_APPEARS_IN_VALUE => 'royksopp',
            org_tubepress_api_const_options_names_Output::VIMEO_SEARCH_VALUE     => 'cats playing piano',
            org_tubepress_api_const_options_names_Output::VIMEO_CREDITED_VALUE   => 'patricklawler',
            org_tubepress_api_const_options_names_Output::VIMEO_CHANNEL_VALUE    => 'splitscreenstuff',
            org_tubepress_api_const_options_names_Output::VIMEO_GROUP_VALUE      => 'hdxs',
            org_tubepress_api_const_options_names_Output::VIMEO_ALBUM_VALUE      => '140484',
            org_tubepress_api_const_options_names_Advanced::CACHE_DIR            => '',
            org_tubepress_api_const_options_names_Feed::SEARCH_ONLY_USER         => '',
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_URL     => '',
            org_tubepress_api_const_options_names_Output::SEARCH_PROVIDER        => 'youtube'
        ),
        org_tubepress_api_const_options_Type::BOOL => array(
            org_tubepress_api_const_options_names_Advanced::DEBUG_ON               => true,
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_CURL      => false,
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_EXTHTTP   => false,
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FOPEN     => false,
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_FSOCKOPEN => false,
            org_tubepress_api_const_options_names_Advanced::DISABLE_HTTP_STREAMS   => false,
            org_tubepress_api_const_options_names_Display::RANDOM_THUMBS           => true,
            org_tubepress_api_const_options_names_Display::RELATIVE_DATES          => false,
            org_tubepress_api_const_options_names_Display::PAGINATE_ABOVE          => true,
            org_tubepress_api_const_options_names_Display::PAGINATE_BELOW          => true,
            org_tubepress_api_const_options_names_Display::AJAX_PAGINATION         => false,
            org_tubepress_api_const_options_names_Display::HQ_THUMBS               => false,
            org_tubepress_api_const_options_names_Embedded::AUTOPLAY               => false,
            org_tubepress_api_const_options_names_Embedded::LOOP                   => false,
            org_tubepress_api_const_options_names_Embedded::SHOW_INFO              => false,
            org_tubepress_api_const_options_names_Embedded::SHOW_RELATED           => true,
            org_tubepress_api_const_options_names_Embedded::FULLSCREEN             => true,
            org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY           => false,
            org_tubepress_api_const_options_names_Meta::AUTHOR                     => false,
            org_tubepress_api_const_options_names_Meta::CATEGORY                   => false,
            org_tubepress_api_const_options_names_Meta::DESCRIPTION                => false,
            org_tubepress_api_const_options_names_Meta::ID                         => false,
            org_tubepress_api_const_options_names_Meta::LENGTH                     => true,
            org_tubepress_api_const_options_names_Meta::RATING                     => false,
            org_tubepress_api_const_options_names_Meta::RATINGS                    => false,
            org_tubepress_api_const_options_names_Meta::TAGS                       => false,
            org_tubepress_api_const_options_names_Meta::TITLE                      => true,
            org_tubepress_api_const_options_names_Meta::UPLOADED                   => false,
            org_tubepress_api_const_options_names_Meta::URL                        => false,
            org_tubepress_api_const_options_names_Meta::VIEWS                      => true,
            org_tubepress_api_const_options_names_Meta::LIKES                      => false,
            org_tubepress_api_const_options_names_Feed::CACHE_ENABLED              => false,
            org_tubepress_api_const_options_names_Feed::EMBEDDABLE_ONLY            => true,
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY      => false
        ),
        org_tubepress_api_const_options_Type::INTEGRAL => array(
            org_tubepress_api_const_options_names_Display::DESC_LIMIT              => 80,
            org_tubepress_api_const_options_names_Display::RESULTS_PER_PAGE        => 20,
            org_tubepress_api_const_options_names_Display::THUMB_HEIGHT            => 90,
            org_tubepress_api_const_options_names_Display::THUMB_WIDTH             => 120,
            org_tubepress_api_const_options_names_Embedded::EMBEDDED_HEIGHT        => 350,
            org_tubepress_api_const_options_names_Embedded::EMBEDDED_WIDTH         => 425,
            org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP           => 300,
            org_tubepress_api_const_options_names_Advanced::CACHE_CLEAN_FACTOR     => 20,
            org_tubepress_api_const_options_names_Advanced::CACHE_LIFETIME_SECONDS => 3600
        ),
        org_tubepress_api_const_options_Type::TIME_FRAME => array(
            org_tubepress_api_const_options_names_Output::MOST_VIEWED_VALUE   => org_tubepress_api_const_options_values_TimeFrameValue::TODAY,
            org_tubepress_api_const_options_names_Output::TOP_RATED_VALUE     => org_tubepress_api_const_options_values_TimeFrameValue::TODAY,
            org_tubepress_api_const_options_names_Output::TOP_FAVORITES_VALUE => org_tubepress_api_const_options_values_TimeFrameValue::TODAY
        ),
        org_tubepress_api_const_options_Type::ORDER => array(
            org_tubepress_api_const_options_names_Display::ORDER_BY => org_tubepress_api_const_options_values_OrderValue::VIEW_COUNT,
        ),
        org_tubepress_api_const_options_Type::PLAYER => array(
            org_tubepress_api_const_options_names_Display::CURRENT_PLAYER_NAME => org_tubepress_api_const_options_values_PlayerValue::NORMAL,
        ),
        org_tubepress_api_const_options_Type::SAFE_SEARCH => array(
            org_tubepress_api_const_options_names_Feed::FILTER => org_tubepress_api_const_options_values_SafeSearchValue::MODERATE
        ),
        org_tubepress_api_const_options_Type::PLAYER_IMPL => array(
            org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL => org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED
        ),
        org_tubepress_api_const_options_Type::THEME => array(
            org_tubepress_api_const_options_names_Display::THEME => ''
        ),
        org_tubepress_api_const_options_Type::OUTPUT => array(
            org_tubepress_api_const_options_names_Output::OUTPUT => ''
        )
    );
	
	function testGetAdvancedOptionNames()
	{
	    $expectedNames = array('cacheCleaningFactor', 'cacheDirectory', 'cacheLifetimeSeconds',
	    	'dateFormat', 'debugging_enabled',  'disableHttpTransportCurl', 'disableHttpTransportExtHttp', 'disableHttpTransportFopen', 'disableHttpTransportFsockOpen', 'disableHttpTransportStreams', 'keyword',
	    	'videoBlacklist'
	    );
	    self::checkArrayEquality($expectedNames, org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_CategoryName::ADVANCED));
	}
    
    function testGetAllOptionNames()
    {
        $expected = array();
        foreach ($this->_options as $optionType) {
            $expected = array_merge($expected, array_keys($optionType));    
        }
	self::checkArrayEquality($expected, org_tubepress_impl_options_OptionsReference::getAllOptionNames());
    }
    
    function testGetDefaultValue()
    {
        foreach ($this->_options as $optionType) {
            foreach ($optionType as $optionName => $defaultValue) {
                $this->assertEquals($defaultValue, org_tubepress_impl_options_OptionsReference::getDefaultValue($optionName));
            }
        }    
    }
    
    function testGetDisplayOptionNames()
    {
         $expectedNames = array('theme', 'ajaxPagination', 'playerLocation', 'resultsPerPage', 'hqThumbs', 'thumbHeight', 'thumbWidth',
         'orderBy', 'paginationAbove', 'paginationBelow', 'randomize_thumbnails', 'relativeDates' ,'descriptionLimit'
         );
         self::checkArrayEquality($expectedNames, org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_CategoryName::DISPLAY));
    }
    
    function testGetEmbeddedOptionNames()
    {
        $expectedNames = array(
            'playerImplementation', 'embeddedHeight', 'embeddedWidth', 
            'autoplay', 'fullscreen', 'hd', 'loop', 'playerColor',
            'playerHighlight', 'showInfo', 'showRelated'
        );
        self::checkArrayEquality($expectedNames, org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_CategoryName::EMBEDDED));  
    }
    
    function testGetFeedOptionNames()
    {
         $expectedNames = array(
             'cacheEnabled', 'embeddableOnly', 'filter_racy', 'developerKey', 'resultCountCap', 'searchResultsRestrictedToUser', 'vimeoKey', 'vimeoSecret'
         );   
         self::checkArrayEquality($expectedNames, org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_CategoryName::FEED));
    }
    
    function testGetOutputOptionNames()
    {
        $expectedNames = array(
            'mode', 'video', 'output',  'favoritesValue', 'most_viewedValue', 'playlistValue', 'tagValue',
            'youtubeTopFavoritesValue', 'top_ratedValue', 'userValue', 'vimeoUploadedByValue', 'vimeoLikesValue',
            'vimeoAppearsInValue', 'vimeoSearchValue', 'vimeoCreditedToValue', 'vimeoChannelValue', 'vimeoAlbumValue', 'vimeoGroupValue', 'searchResultsUrl',
            org_tubepress_api_const_options_names_Output::SEARCH_RESULTS_ONLY,
            org_tubepress_api_const_options_names_Output::SEARCH_PROVIDER
        );
        self::checkArrayEquality($expectedNames, org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_CategoryName::OUTPUT));
    }
    
    function testGetMetaOptionNames()
    {
        $expectedNames = array(
            'author', 'category', 'description', 'id',
            'length', 'likes', 'rating', 'ratings', 'tags',
            'title', 'uploaded', 'url', 'views'
        );
        self::checkArrayEquality($expectedNames, org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_CategoryName::META));
    }
    
    function testIsOptionName()
    {
        $expected = array();
        foreach ($this->_options as $optionType) {
            foreach($optionType as $optionName => $value) {
                $this->assertTrue(org_tubepress_impl_options_OptionsReference::isOptionName($optionName));
            }    
        }
        $this->assertFalse(org_tubepress_impl_options_OptionsReference::isOptionName('obviously fake option name'));
    }
    
    function testGetWidgetOptionNames()
    {
        $expectedNames = array(
            'widget-tagstring', 'widget-title'
        );
        self::checkArrayEquality($expectedNames, org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_CategoryName::WIDGET));
    }
    function testGetType()
    {
        $types = array_keys($this->_options);
        for ($x = 0; $x < sizeof($types); $x++) {
            foreach ($this->_options[$types[$x]] as $optionName => $value) {
                $this->assertEquals($types[$x], org_tubepress_impl_options_OptionsReference::getType($optionName));
            }            
        }    
    }
    function testGetOptionCategoryNames()
    {
        $expected = array('display', 'embedded', 'meta', 'feed', 'advanced', 'output', 'widget');
        self::checkArrayEquality($expected, org_tubepress_impl_options_OptionsReference::getOptionCategoryNames());
    }
    function testGetCategory()
    {
        $categories = array('output', 'display', 'embedded', 'feed', 'meta', 'widget');
        foreach ($categories as $category) {
            $classname = 'org_tubepress_api_const_options_names_' . ucwords($category);
            $ref = new ReflectionClass($classname);
            foreach ($ref->getConstants() as $constant) {
                $this->assertEquals($category, org_tubepress_impl_options_OptionsReference::getCategory($constant));
            }
        }
    }
    function testPlayerEnumValues()
    {
        $expected = array('normal', 'popup','shadowbox', 'jqmodal', 'youtube', 'static', 'solo', 'vimeo', 'fancybox', 'tinybox');
        self::checkArrayEquality($expected, org_tubepress_impl_options_OptionsReference::getValidEnumValues(org_tubepress_api_const_options_Type::PLAYER));
    }
    function testMostViewedEnumValues()
    {
        $expected = array('today', 'this_week', 'this_month', 'all_time');
        self::checkArrayEquality($expected, org_tubepress_impl_options_OptionsReference::getValidEnumValues(org_tubepress_api_const_options_Type::TIME_FRAME));
    }
    function testOrderEnumValues()
    {
        $expected = array('relevance', 'viewCount', 'rating', 'published', 'random', 'position', 'commentCount', 'duration', 'title', 'newest', 'oldest');
        self::checkArrayEquality($expected, org_tubepress_impl_options_OptionsReference::getValidEnumValues(org_tubepress_api_const_options_Type::ORDER));
    }
    
    function testGalleryEnumValues()
    {
        $expected = array(org_tubepress_api_const_options_values_ModeValue::FAVORITES, org_tubepress_api_const_options_values_ModeValue::PLAYLIST, org_tubepress_api_const_options_values_ModeValue::TAG, org_tubepress_api_const_options_values_ModeValue::USER, org_tubepress_api_const_options_values_ModeValue::FEATURED, org_tubepress_api_const_options_values_ModeValue::MOST_DISCUSSED,
                    org_tubepress_api_const_options_values_ModeValue::MOST_RECENT, org_tubepress_api_const_options_values_ModeValue::MOST_RESPONDED, org_tubepress_api_const_options_values_ModeValue::POPULAR, org_tubepress_api_const_options_values_ModeValue::TOP_FAVORITES,
                    org_tubepress_api_const_options_values_ModeValue::TOP_RATED, 'vimeoUploadedBy', 'vimeoLikes', 'vimeoAppearsIn', 'vimeoSearch', 'vimeoCreditedTo',
                    'vimeoChannel', 'vimeoAlbum', 'vimeoGroup');
        self::checkArrayEquality($expected, org_tubepress_impl_options_OptionsReference::getValidEnumValues(org_tubepress_api_const_options_Type::MODE));
    }
}
?>
