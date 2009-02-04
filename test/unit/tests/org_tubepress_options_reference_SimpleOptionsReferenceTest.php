<?php
class org_tubepress_options_reference_SimpleOptionsReferenceTest extends PHPUnit_Framework_TestCase {
    
	private $_sut;
	
	    private $_options = array(
        org_tubepress_options_Type::TEXT => array(
            org_tubepress_options_category_Advanced::DATEFORMAT => "M j, Y",
            org_tubepress_options_category_Advanced::KEYWORD	=> "tubepress",
            org_tubepress_options_category_Embedded::PLAYER_COLOR => "/",
            org_tubepress_options_category_Gallery::MODE             => "recently_featured",
            org_tubepress_options_category_Gallery::FAVORITES_VALUE   => "mrdeathgod",
            org_tubepress_options_category_Gallery::PLAYLIST_VALUE    => "D2B04665B213AE35",
            org_tubepress_options_category_Gallery::TAG_VALUE         => "stewart daily show",
            org_tubepress_options_category_Gallery::USER_VALUE        => "3hough",
            org_tubepress_options_category_YouTubeFeed::CLIENT_KEY    => "ytapi-EricHough-TubePress-ki6oq9tc-0",
            org_tubepress_options_category_YouTubeFeed::DEV_KEY => "AI39si5uUzupiQW9bpzGqZRrhvqF3vBgRqL-I_28G1zWozmdNJlskzMDQEhpZ-l2RqGf_6CNWooL96oJZRrqKo-eJ9QO_QppMg",
            org_tubepress_options_category_Widget::TITLE     => "TubePress",
            org_tubepress_options_category_Widget::TAGSTRING => "[tubepress thumbHeight='105', thumbWidth='135']"
        ),
        org_tubepress_options_Type::BOOL => array(
            org_tubepress_options_category_Advanced::DEBUG_ON   => true,
            org_tubepress_options_category_Advanced::NOFOLLOW_LINKS => true,
            org_tubepress_options_category_Advanced::RANDOM_THUMBS => true,
            org_tubepress_options_category_Display::RELATIVE_DATES	=> false,
            org_tubepress_options_category_Embedded::AUTOPLAY    => false,
            org_tubepress_options_category_Embedded::BORDER      => false,
            org_tubepress_options_category_Embedded::GENIE	      => false,
            org_tubepress_options_category_Embedded::LOOP	      => false,
            org_tubepress_options_category_Embedded::SHOW_RELATED => true,
            org_tubepress_options_category_Embedded::FULLSCREEN  => true,
            org_tubepress_options_category_Meta::AUTHOR      => false,
            org_tubepress_options_category_Meta::CATEGORY    => false,
            org_tubepress_options_category_Meta::DESCRIPTION => false,
            org_tubepress_options_category_Meta::ID          => false,
            org_tubepress_options_category_Meta::LENGTH      => true,
            org_tubepress_options_category_Meta::RATING      => false,
            org_tubepress_options_category_Meta::RATINGS     => false,
            org_tubepress_options_category_Meta::TAGS        => false,
            org_tubepress_options_category_Meta::TITLE       => true,
            org_tubepress_options_category_Meta::UPLOADED    => false,
            org_tubepress_options_category_Meta::URL        => false,
            org_tubepress_options_category_Meta::VIEWS       => true,
            org_tubepress_options_category_YouTubeFeed::FILTER   => false,
            org_tubepress_options_category_YouTubeFeed::CACHE_ENABLED  => true,
            org_tubepress_options_category_YouTubeFeed::EMBEDDABLE_ONLY => true
        ),
        org_tubepress_options_Type::INTEGRAL => array(
            org_tubepress_options_category_Display::DESC_LIMIT  => 80,
            org_tubepress_options_category_Display::RESULTS_PER_PAGE	=> 20,
            org_tubepress_options_category_Display::THUMB_HEIGHT		=> 90,
            org_tubepress_options_category_Display::THUMB_WIDTH		=> 120,
            org_tubepress_options_category_Embedded::EMBEDDED_HEIGHT => 355,
            org_tubepress_options_category_Embedded::EMBEDDED_WIDTH	 => 425
        ),
        org_tubepress_options_Type::ENUM => array(
            org_tubepress_options_category_Display::CURRENT_PLAYER_NAME => "normal",
            org_tubepress_options_category_Display::ORDER_BY		 => "viewCount",
            org_tubepress_options_category_Embedded::QUALITY	  => "normal",
            org_tubepress_options_category_Gallery::MOST_VIEWED_VALUE => "today",
            org_tubepress_options_category_Gallery::TOP_RATED_VALUE   => "today"
        )
    );
	
	function setUp()
	{
		$this->_sut = new org_tubepress_options_reference_SimpleOptionsReference();
	}
	
	function testGetAdvancedOptionNames()
	{
	    $expectedNames = array(
	    	"dateFormat", "debugging_enabled", "keyword",
	    	"randomize_thumbnails", "nofollowLinks"
	    );
	    $this->assertTrue($expectedNames == $this->_sut->getAdvancedOptionNames());
	}
    
    function testGetAllOptionNames()
    {
        $expected = array();
        foreach ($this->_options as $optionType) {
            array_push($expected, array_keys($optionType));    
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
         $expectedNames = array(
            "playerLocation","descriptionLimit", "orderBy", 
            "relativeDates", "resultsPerPage", "thumbHeight", "thumbWidth");
         $this->assertTrue($expectedNames == $this->_sut->getDisplayOptionNames());   
    }
    
    function testGetEmbeddedOptionNames()
    {
        $expectedNames = array(
            "autoplay", "border", "embeddedHeight",
            "embeddedWidth", "fullscreen", "genie",
            "loop", "playerColor", "quality", "showRelated"
        );
        $this->assertTrue($expectedNames == $this->_sut->getEmbeddedOptionNames());   
    }
    
    function testGetFeedOptionNames()
    {
         $expectedNames = array(
             "filter_racy", "clientKey", "developerKey",
             "cacheEnabled", "embeddableOnly"
         );   
         $this->assertTrue($expectedNames == $this->_sut->getFeedOptionNames()); 
    }
    
    function testGetGalleryOptionNames()
    {
        $expectedNames = array(
            "mode", "favoritesValue", "most_viewedValue", "playlistValue",
            "tagValue", "top_ratedValue", "userValue"
        );
        $this->assertTrue($expectedNames == $this->_sut->getGalleryOptionNames());
    }
    
    function testGetMetaOptionNames()
    {
        $expectedNames = array(
            "author", "category", "description", "id",
            "length", "rating", "ratings", "tags",
            "title", "uploaded", "url", "views"
        );
        $this->assertTrue($expectedNames == $this->_sut->getMetaOptionNames());
    }
    
    function testIsOptionName()
    {
        $expected = array();
        foreach ($this->_options as $optionType) {
            foreach($optionType as $optionName => $value) {
                $this->assertTrue($this->_sut->isOptionName($optionName));
            }    
        }
        $this->assertFalse($this->_sut->isOptionName("obviously fake option name"));
    }
    
    function testGetWidgetOptionNames()
    {
        $expectedNames = array(
            "widget-title", "widget-tagstring"
        );
        $this->assertTrue($expectedNames == $this->_sut->getWidgetOptionNames());
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
}
?>