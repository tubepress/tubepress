<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/options/OptionsReference.class.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/api/const/options/OptionCategory.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_impl_options_OptionsReferenceTest extends TubePressUnitTest {
    
    private $_options = array(
        org_tubepress_api_const_options_OptionType::COLOR => array(
            org_tubepress_api_const_options_Embedded::PLAYER_COLOR   => '999999',
            org_tubepress_api_const_options_Embedded::PLAYER_HIGHLIGHT => 'FFFFFF'
        ),
        org_tubepress_api_const_options_OptionType::MODE => array(
            org_tubepress_api_const_options_Gallery::MODE => 'recently_featured'
        ),
        org_tubepress_api_const_options_OptionType::TEXT => array(
            org_tubepress_api_const_options_Advanced::DATEFORMAT            => 'M j, Y',
            org_tubepress_api_const_options_Advanced::KEYWORD               => 'tubepress',
            org_tubepress_api_const_options_Advanced::VIDEO_BLACKLIST       => '',
            org_tubepress_api_const_options_Gallery::FAVORITES_VALUE        => 'mrdeathgod',
            org_tubepress_api_const_options_Gallery::PLAYLIST_VALUE         => 'D2B04665B213AE35',
            org_tubepress_api_const_options_Gallery::TAG_VALUE              => 'stewart daily show',
            org_tubepress_api_const_options_Gallery::USER_VALUE             => '3hough',
            org_tubepress_api_const_options_Feed::DEV_KEY                   => 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg',
            org_tubepress_api_const_options_Feed::VIMEO_KEY                 => '',
            org_tubepress_api_const_options_Feed::VIMEO_SECRET              => '',
            org_tubepress_api_const_options_Widget::TITLE                   => 'TubePress',
            org_tubepress_api_const_options_Widget::TAGSTRING               => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']',
            org_tubepress_api_const_options_Gallery::VIDEO                  => '',
            org_tubepress_api_const_options_Gallery::VIMEO_UPLOADEDBY_VALUE => 'mattkaar',
            org_tubepress_api_const_options_Gallery::VIMEO_LIKES_VALUE      => 'coiffier',
            org_tubepress_api_const_options_Gallery::VIMEO_APPEARS_IN_VALUE => 'royksopp',
            org_tubepress_api_const_options_Gallery::VIMEO_SEARCH_VALUE     => 'cats playing piano',
            org_tubepress_api_const_options_Gallery::VIMEO_CREDITED_VALUE   => 'patricklawler',
            org_tubepress_api_const_options_Gallery::VIMEO_CHANNEL_VALUE    => 'splitscreenstuff',
            org_tubepress_api_const_options_Gallery::VIMEO_GROUP_VALUE      => 'hdxs',
            org_tubepress_api_const_options_Gallery::VIMEO_ALBUM_VALUE      => '140484',
            org_tubepress_api_const_options_Advanced::CACHE_DIR             => '',
            org_tubepress_api_const_options_Feed::SEARCH_ONLY_USER          => ''
        ),
        org_tubepress_api_const_options_OptionType::BOOL => array(
            org_tubepress_api_const_options_Advanced::DEBUG_ON               => true,
            org_tubepress_api_const_options_Advanced::DISABLE_HTTP_CURL      => false,
            org_tubepress_api_const_options_Advanced::DISABLE_HTTP_EXTHTTP   => false,
            org_tubepress_api_const_options_Advanced::DISABLE_HTTP_FOPEN     => false,
            org_tubepress_api_const_options_Advanced::DISABLE_HTTP_FSOCKOPEN => false,
            org_tubepress_api_const_options_Advanced::DISABLE_HTTP_STREAMS   => false,
            org_tubepress_api_const_options_Display::RANDOM_THUMBS           => true,
            org_tubepress_api_const_options_Display::RELATIVE_DATES          => false,
            org_tubepress_api_const_options_Display::PAGINATE_ABOVE          => true,
            org_tubepress_api_const_options_Display::PAGINATE_BELOW          => true,
            org_tubepress_api_const_options_Display::AJAX_PAGINATION         => false,
            org_tubepress_api_const_options_Display::HQ_THUMBS               => false,
            org_tubepress_api_const_options_Embedded::AUTOPLAY               => false,
            org_tubepress_api_const_options_Embedded::BORDER                 => false,
            org_tubepress_api_const_options_Embedded::GENIE                  => false,
            org_tubepress_api_const_options_Embedded::LOOP                   => false,
            org_tubepress_api_const_options_Embedded::SHOW_INFO              => false,
            org_tubepress_api_const_options_Embedded::SHOW_RELATED           => true,
            org_tubepress_api_const_options_Embedded::FULLSCREEN             => true,
            org_tubepress_api_const_options_Embedded::HIGH_QUALITY           => false,
            org_tubepress_api_const_options_Meta::AUTHOR                     => false,
            org_tubepress_api_const_options_Meta::CATEGORY                   => false,
            org_tubepress_api_const_options_Meta::DESCRIPTION                => false,
            org_tubepress_api_const_options_Meta::ID                         => false,
            org_tubepress_api_const_options_Meta::LENGTH                     => true,
            org_tubepress_api_const_options_Meta::RATING                     => false,
            org_tubepress_api_const_options_Meta::RATINGS                    => false,
            org_tubepress_api_const_options_Meta::TAGS                       => false,
            org_tubepress_api_const_options_Meta::TITLE                      => true,
            org_tubepress_api_const_options_Meta::UPLOADED                   => false,
            org_tubepress_api_const_options_Meta::URL                        => false,
            org_tubepress_api_const_options_Meta::VIEWS                      => true,
            org_tubepress_api_const_options_Meta::LIKES                      => false,
            org_tubepress_api_const_options_Feed::CACHE_ENABLED              => false,
            org_tubepress_api_const_options_Feed::EMBEDDABLE_ONLY            => true
        ),
        org_tubepress_api_const_options_OptionType::INTEGRAL => array(
            org_tubepress_api_const_options_Display::DESC_LIMIT              => 80,
            org_tubepress_api_const_options_Display::RESULTS_PER_PAGE        => 20,
            org_tubepress_api_const_options_Display::THUMB_HEIGHT            => 90,
            org_tubepress_api_const_options_Display::THUMB_WIDTH             => 120,
            org_tubepress_api_const_options_Embedded::EMBEDDED_HEIGHT        => 350,
            org_tubepress_api_const_options_Embedded::EMBEDDED_WIDTH         => 425,
            org_tubepress_api_const_options_Feed::RESULT_COUNT_CAP           => 300,
            org_tubepress_api_const_options_Advanced::CACHE_CLEAN_FACTOR     => 20,
            org_tubepress_api_const_options_Advanced::CACHE_LIFETIME_SECONDS => 3600
            
        ),
        org_tubepress_api_const_options_OptionType::TIME_FRAME => array(
            org_tubepress_api_const_options_Gallery::MOST_VIEWED_VALUE   => 'today',
            org_tubepress_api_const_options_Gallery::TOP_RATED_VALUE     => 'today',
            org_tubepress_api_const_options_Gallery::TOP_FAVORITES_VALUE => 'today'
        ),
        org_tubepress_api_const_options_OptionType::ORDER => array(
            org_tubepress_api_const_options_Display::ORDER_BY            => 'viewCount',
        ),
        org_tubepress_api_const_options_OptionType::PLAYER => array(
            org_tubepress_api_const_options_Display::CURRENT_PLAYER_NAME => 'normal',
        ),
        org_tubepress_api_const_options_OptionType::SAFE_SEARCH => array(
            org_tubepress_api_const_options_Feed::FILTER                 => 'moderate'    
        ),
        org_tubepress_api_const_options_OptionType::PLAYER_IMPL => array(
            org_tubepress_api_const_options_Embedded::PLAYER_IMPL        => 'youtube'
        ),
        org_tubepress_api_const_options_OptionType::THEME => array(
            org_tubepress_api_const_options_Display::THEME => ''
        )
    );
	
	function testGetAdvancedOptionNames()
	{
	    $expectedNames = array('cacheCleaningFactor', 'cacheDirectory', 'cacheLifetimeSeconds',
	    	'dateFormat', 'debugging_enabled',  'disableHttpTransportCurl', 'disableHttpTransportExtHttp', 'disableHttpTransportFopen', 'disableHttpTransportFsockOpen', 'disableHttpTransportStreams', 'keyword',
	    	'videoBlacklist'
	    );
	    $this->assertTrue($expectedNames == org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_OptionCategory::ADVANCED));
	}
    
    function testGetAllOptionNames()
    {
        $expected = array();
        foreach ($this->_options as $optionType) {
            $expected = array_merge($expected, array_keys($optionType));    
        }
        $this->assertTrue($expected == org_tubepress_impl_options_OptionsReference::getAllOptionNames());   
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
         $this->assertTrue($expectedNames == org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_OptionCategory::DISPLAY));
    }
    
    function testGetEmbeddedOptionNames()
    {
        $expectedNames = array(
            'playerImplementation', 'embeddedHeight', 'embeddedWidth', 
            'autoplay', 'border', 'fullscreen', 'hd', 'genie', 'loop', 'playerColor',
            'playerHighlight', 'showInfo', 'showRelated'
        );
        $this->assertTrue($expectedNames == org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_OptionCategory::EMBEDDED));  
    }
    
    function testGetFeedOptionNames()
    {
         $expectedNames = array(
             'cacheEnabled', 'embeddableOnly', 'filter_racy', 'developerKey', 'resultCountCap', 'searchResultsRestrictedToUser', 'vimeoKey', 'vimeoSecret'
         );   
         $this->assertTrue($expectedNames == org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_OptionCategory::FEED));
    }
    
    function testGetGalleryOptionNames()
    {
        $expectedNames = array(
            'mode', 'video', 'favoritesValue', 'most_viewedValue', 'playlistValue',
            'tagValue', 'youtubeTopFavoritesValue', 'top_ratedValue', 'userValue', 'vimeoUploadedByValue','vimeoLikesValue',
            'vimeoAppearsInValue', 'vimeoSearchValue', 'vimeoCreditedToValue', 'vimeoChannelValue',
            'vimeoAlbumValue', 'vimeoGroupValue'
        );
        $this->assertTrue($expectedNames == org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_OptionCategory::GALLERY));
    }
    
    function testGetMetaOptionNames()
    {
        $expectedNames = array(
            'author', 'category', 'description', 'id',
            'length', 'likes', 'rating', 'ratings', 'tags',
            'title', 'uploaded', 'url', 'views'
        );
        $this->assertTrue($expectedNames == org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_OptionCategory::META));
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
        $this->assertTrue($expectedNames == org_tubepress_impl_options_OptionsReference::getOptionNamesForCategory(org_tubepress_api_const_options_OptionCategory::WIDGET));
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
        $expected = array('gallery', 'display', 'embedded', 'meta', 'feed', 'advanced', 'widget');
        $this->assertTrue($expected == org_tubepress_impl_options_OptionsReference::getOptionCategoryNames());
    }
    function testGetCategory()
    {
        $categories = array('gallery', 'display', 'embedded', 'feed', 'meta', 'widget');
        foreach ($categories as $category) {
            $classname = 'org_tubepress_api_const_options_' . ucwords($category);
            $ref = new ReflectionClass($classname);
            foreach ($ref->getConstants() as $constant) {
                $this->assertEquals($category, org_tubepress_impl_options_OptionsReference::getCategory($constant));
            }
        }
    }
    function testPlayerEnumValues()
    {
        $expected = array('normal', 'popup','shadowbox', 'jqmodal', 'youtube', 'static', 'solo', 'vimeo', 'fancybox', 'tinybox');
        $this->assertEquals($expected, org_tubepress_impl_options_OptionsReference::getValidEnumValues(org_tubepress_api_const_options_OptionType::PLAYER));
    }
    function testMostViewedEnumValues()
    {
        $expected = array('today', 'this_week', 'this_month', 'all_time');
        $this->assertEquals($expected, org_tubepress_impl_options_OptionsReference::getValidEnumValues(org_tubepress_api_const_options_OptionType::TIME_FRAME));
    }
    function testOrderEnumValues()
    {
        $expected = array('relevance', 'viewCount', 'rating', 'published', 'random', 'position', 'commentCount', 'duration', 'title', 'newest', 'oldest');
        $this->assertEquals($expected, org_tubepress_impl_options_OptionsReference::getValidEnumValues(org_tubepress_api_const_options_OptionType::ORDER));
    }
    
    function testGalleryEnumValues()
    {
        $expected = array('favorites', 'playlist', 'tag', 'user', 'recently_featured', 'mobile', 'most_discussed',
                    'most_recent', 'most_responded', 'most_viewed', 'youtubeTopFavorites',
                    'top_rated', 'vimeoUploadedBy', 'vimeoLikes', 'vimeoAppearsIn', 'vimeoSearch', 'vimeoCreditedTo',
                    'vimeoChannel', 'vimeoAlbum', 'vimeoGroup');
        $this->assertEquals($expected, org_tubepress_impl_options_OptionsReference::getValidEnumValues(org_tubepress_api_const_options_OptionType::MODE));
    }
}
?>
