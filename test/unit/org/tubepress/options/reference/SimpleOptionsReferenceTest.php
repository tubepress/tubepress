<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/reference/SimpleOptionsReference.class.php';
require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/options/Category.class.php';

class org_tubepress_options_reference_SimpleOptionsReferenceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
    private $_options = array(
        org_tubepress_options_Type::COLOR => array(
            org_tubepress_options_category_Embedded::PLAYER_COLOR   => '999999',
            org_tubepress_options_category_Embedded::PLAYER_HIGHLIGHT => 'FFFFFF'
        ),
        org_tubepress_options_Type::MODE => array(
            org_tubepress_options_category_Gallery::MODE => 'recently_featured'
        ),
        org_tubepress_options_Type::TEXT => array(
            org_tubepress_options_category_Advanced::DATEFORMAT     => 'M j, Y',
            org_tubepress_options_category_Advanced::KEYWORD        => 'tubepress',
            org_tubepress_options_category_Gallery::FAVORITES_VALUE => 'mrdeathgod',
            org_tubepress_options_category_Gallery::PLAYLIST_VALUE  => 'D2B04665B213AE35',
            org_tubepress_options_category_Gallery::TAG_VALUE       => 'stewart daily show',
            org_tubepress_options_category_Gallery::USER_VALUE      => '3hough',
            org_tubepress_options_category_Feed::CLIENT_KEY         => 'ytapi-EricHough-TubePress-ki6oq9tc-0',
            org_tubepress_options_category_Feed::DEV_KEY            => 'AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg',
            org_tubepress_options_category_Widget::TITLE            => 'TubePress',
            org_tubepress_options_category_Widget::TAGSTRING        => '[tubepress thumbHeight=\'105\' thumbWidth=\'135\']',
            org_tubepress_options_category_Template::TEMPLATE       => ''
        ),
        org_tubepress_options_Type::BOOL => array(
            org_tubepress_options_category_Advanced::DEBUG_ON           => true,
            org_tubepress_options_category_Advanced::RANDOM_THUMBS      => true,
            org_tubepress_options_category_Display::RELATIVE_DATES      => false,
            org_tubepress_options_category_Display::PAGINATE_ABOVE      => true,
            org_tubepress_options_category_Display::PAGINATE_BELOW      => true,
            org_tubepress_options_category_Display::AJAX_PAGINATION     => false,
            org_tubepress_options_category_Embedded::AUTOPLAY           => false,
            org_tubepress_options_category_Embedded::BORDER             => false,
            org_tubepress_options_category_Embedded::GENIE              => false,
            org_tubepress_options_category_Embedded::LOOP               => false,
            org_tubepress_options_category_Embedded::SHOW_INFO          => false,
            org_tubepress_options_category_Embedded::SHOW_RELATED       => true,
            org_tubepress_options_category_Embedded::FULLSCREEN         => true,
            org_tubepress_options_category_Embedded::HIGH_QUALITY       => false,
            org_tubepress_options_category_Meta::AUTHOR                 => false,
            org_tubepress_options_category_Meta::CATEGORY               => false,
            org_tubepress_options_category_Meta::DESCRIPTION            => false,
            org_tubepress_options_category_Meta::ID                     => false,
            org_tubepress_options_category_Meta::LENGTH                 => true,
            org_tubepress_options_category_Meta::RATING                 => false,
            org_tubepress_options_category_Meta::RATINGS                => false,
            org_tubepress_options_category_Meta::TAGS                   => false,
            org_tubepress_options_category_Meta::TITLE                  => true,
            org_tubepress_options_category_Meta::UPLOADED               => false,
            org_tubepress_options_category_Meta::URL                    => false,
            org_tubepress_options_category_Meta::VIEWS                  => true,
            org_tubepress_options_category_Feed::CACHE_ENABLED          => true,
            org_tubepress_options_category_Feed::EMBEDDABLE_ONLY        => true
        ),
        org_tubepress_options_Type::INTEGRAL => array(
            org_tubepress_options_category_Display::DESC_LIMIT          => 80,
            org_tubepress_options_category_Display::RESULTS_PER_PAGE    => 20,
            org_tubepress_options_category_Display::THUMB_HEIGHT        => 90,
            org_tubepress_options_category_Display::THUMB_WIDTH         => 120,
            org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT    => 350,
            org_tubepress_options_category_Embedded::EMBEDDED_WIDTH     => 425,
            org_tubepress_options_category_Feed::RESULT_COUNT_CAP       => 300
        ),
        org_tubepress_options_Type::TIME_FRAME => array(
            org_tubepress_options_category_Gallery::MOST_VIEWED_VALUE   => 'today',
            org_tubepress_options_category_Gallery::TOP_RATED_VALUE     => 'today'
        ),
        org_tubepress_options_Type::ORDER => array(
            org_tubepress_options_category_Display::ORDER_BY            => 'viewCount',
        ),
        org_tubepress_options_Type::PLAYER => array(
            org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => 'normal',
        ),
        org_tubepress_options_Type::SAFE_SEARCH => array(
            org_tubepress_options_category_Feed::FILTER                 => 'moderate'    
        ),
        org_tubepress_options_Type::PLAYER_IMPL => array(
            org_tubepress_options_category_Embedded::PLAYER_IMPL        => 'youtube'
        )
    );
	
	
	function setUp()
	{
		$this->_sut = new org_tubepress_options_reference_SimpleOptionsReference();
	}
	
	function testGetAdvancedOptionNames()
	{
	    $expectedNames = array(
	    	'dateFormat', 'debugging_enabled', 'keyword',
	    	'randomize_thumbnails'
	    );
	    $this->assertTrue($expectedNames == $this->_sut->getOptionNamesForCategory(org_tubepress_options_Category::ADVANCED));
	}
    
    function testGetAllOptionNames()
    {
        $expected = array();
        foreach ($this->_options as $optionType) {
            $expected = array_merge($expected, array_keys($optionType));    
        }
        $this->assertTrue($expected == $this->_sut->getAllOptionNames());   
    }
    
    function testGetDefaultValue()
    {
        foreach ($this->_options as $optionType) {
            foreach ($optionType as $optionName => $defaultValue) {
                $this->assertEquals($defaultValue, $this->_sut->getDefaultValue($optionName));
            }
        }    
    }
    
    function testGetDisplayOptionNames()
    {
         $expectedNames = array('ajaxPagination', 'playerLocation', 'resultsPerPage', 'thumbHeight', 'thumbWidth',
         'orderBy', 'paginationAbove', 'paginationBelow', 'relativeDates' ,'descriptionLimit'  
         );
         $this->assertTrue($expectedNames == $this->_sut->getOptionNamesForCategory(org_tubepress_options_Category::DISPLAY));
    }
    
    function testGetEmbeddedOptionNames()
    {
        $expectedNames = array(
            'playerImplementation', 'embeddedHeight', 'embeddedWidth', 
            'autoplay', 'border', 'fullscreen', 'hd', 'genie', 'loop', 'playerColor',
            'playerHighlight', 'showInfo', 'showRelated'
        );
        $this->assertTrue($expectedNames == $this->_sut->getOptionNamesForCategory(org_tubepress_options_Category::EMBEDDED));  
    }
    
    function testGetFeedOptionNames()
    {
         $expectedNames = array(
             'cacheEnabled', 'embeddableOnly', 'filter_racy', 'clientKey', 'developerKey', 'resultCountCap'
         );   
         $this->assertTrue($expectedNames == $this->_sut->getOptionNamesForCategory(org_tubepress_options_Category::FEED));
    }
    
    function testGetGalleryOptionNames()
    {
        $expectedNames = array(
            'mode', 'template', 'favoritesValue', 'most_viewedValue', 'playlistValue',
            'tagValue', 'top_ratedValue', 'userValue'
        );
        $this->assertTrue($expectedNames == $this->_sut->getOptionNamesForCategory(org_tubepress_options_Category::GALLERY));
    }
    
    function testGetMetaOptionNames()
    {
        $expectedNames = array(
            'author', 'category', 'description', 'id',
            'length', 'rating', 'ratings', 'tags',
            'title', 'uploaded', 'url', 'views'
        );
        $this->assertTrue($expectedNames == $this->_sut->getOptionNamesForCategory(org_tubepress_options_Category::META));
    }
    
    function testIsOptionName()
    {
        $expected = array();
        foreach ($this->_options as $optionType) {
            foreach($optionType as $optionName => $value) {
                $this->assertTrue($this->_sut->isOptionName($optionName));
            }    
        }
        $this->assertFalse($this->_sut->isOptionName('obviously fake option name'));
    }
    
    function testGetWidgetOptionNames()
    {
        $expectedNames = array(
            'widget-tagstring', 'widget-title'
        );
        $this->assertTrue($expectedNames == $this->_sut->getOptionNamesForCategory(org_tubepress_options_Category::WIDGET));
    }
    function testGetType()
    {
        $types = array_keys($this->_options);
        for ($x = 0; $x < sizeof($types); $x++) {
            foreach ($this->_options[$types[$x]] as $optionName => $value) {
                $this->assertEquals($types[$x], $this->_sut->getType($optionName));
            }            
        }    
    }
    function testGetOptionCategoryNames()
    {
        $expected = array('gallery', 'display', 'embedded', 'meta', 'feed', 'advanced', 'widget');
        $this->assertTrue($expected == $this->_sut->getOptionCategoryNames());
    }
    function testGetCategory()
    {
        $categories = array('gallery', 'display', 'embedded', 'feed', 'meta', 'widget');
        foreach ($categories as $category) {
            $classname = 'org_tubepress_options_category_' . ucwords($category);
            $ref = new ReflectionClass($classname);
            foreach ($ref->getConstants() as $constant) {
                $this->assertEquals($category, $this->_sut->getCategory($constant));
            }
        }
    }
    function testPlayerEnumValues()
    {
        $expected = array('normal', 'popup','shadowbox', 'jqmodal', 'youtube', 'static');
        $this->assertEquals($expected, $this->_sut->getValidEnumValues(org_tubepress_options_Type::PLAYER));
    }
    function testMostViewedEnumValues()
    {
        $expected = array('today', 'this_week', 'this_month', 'all_time');
        $this->assertEquals($expected, $this->_sut->getValidEnumValues(org_tubepress_options_Type::TIME_FRAME));
    }
    function testOrderEnumValues()
    {
        $expected = array('relevance', 'viewCount', 'rating', 'published', 'random');
        $this->assertEquals($expected, $this->_sut->getValidEnumValues(org_tubepress_options_Type::ORDER));
    }
    
    function testGalleryEnumValues()
    {
        $expected = array('favorites', 'playlist', 'tag', 'user', 'recently_featured', 'mobile', 'most_discussed',
                    'most_linked', 'most_recent', 'most_responded', 'most_viewed',
                    'top_rated');
        $this->assertEquals($expected, $this->_sut->getValidEnumValues(org_tubepress_options_Type::MODE));
    }
}
?>