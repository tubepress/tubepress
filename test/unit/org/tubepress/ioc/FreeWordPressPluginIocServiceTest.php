<?php

require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';

function get_option() { }
function update_option() { }

class org_tubepress_ioc_FreeWordPressPluginIocServiceTest extends TubePressUnitTest {

    private $_sut;
    private $_expectedMapping;
    
    function setUp()
    {
        $this->_sut = new org_tubepress_ioc_impl_FreeWordPressPluginIocService();
        $this->_expectedMapping = array(
            'org_tubepress_api_http_AgentDetector'                     => 'org_tubepress_impl_http_MobileEspBrowserDetector',
            'org_tubepress_api_cache_Cache'                          => 'org_tubepress_impl_cache_PearCacheLiteCacheService',
            'org_tubepress_api_embedded_EmbeddedPlayer'              => 'org_tubepress_impl_embedded_DelegatingEmbeddedPlayer',
            'org_tubepress_api_gallery_Gallery'                             => 'org_tubepress_impl_gallery_SimpleGallery',
            'org_tubepress_api_message_MessageService'                      => 'org_tubepress_impl_message_WordPressMessageService',
            'org_tubepress_api_options_OptionsManager'              => 'org_tubepress_options_manager_SimpleOptionsManager',    
            'org_tubepress_api_options_StorageManager'              => 'org_tubepress_options_storage_WordPressStorageManager',
            'org_tubepress_api_options_OptionValidator'   => 'org_tubepress_options_validation_SimpleInputValidationService',    
            'org_tubepress_api_pagination_Pagination'                => 'org_tubepress_pagination_DiggStylePaginationService',
            'org_tubepress_api_player_Player'                               => 'org_tubepress_player_SimplePlayer',
            'org_tubepress_api_querystring_QueryStringService'              => 'org_tubepress_impl_querystring_SimpleQueryStringService',
            'org_tubepress_api_shortcode_ShortcodeParser'                   => 'org_tubepress_shortcode_SimpleShortcodeParser',
            'org_tubepress_api_single_SingleVideo'                          => 'org_tubepress_single_SimpleSingleVideo',
            'org_tubepress_api_theme_ThemeHandler'                          => 'org_tubepress_impl_theme_SimpleThemeHandler',
            'org_tubepress_api_feed_UrlBuilder'                              => 'org_tubepress_url_impl_DelegatingUrlBuilder',
            'org_tubepress_api_feed_VideoFactory'                  => 'org_tubepress_video_factory_DelegatingVideoFactory',
            'org_tubepress_api_feed_FeedInspector' => 'org_tubepress_video_feed_inspection_DelegatingFeedInspectionService',
            'org_tubepress_api_provider_Provider'                => 'org_tubepress_video_feed_provider_SimpleProvider',
            'org_tubepress_api_feed_FeedFetcher'   => 'org_tubepress_video_feed_retrieval_HTTPRequest2',
        
        );
    }

    function testMapping()
    {
        foreach ($this->_expectedMapping as $key => $value) {
            $test = is_a($this->_sut->get($key), $value);
            if (!$test) {
                print "$key is not a $value\n";
            }
            $this->assertTrue($test);
        }
    }
}
?>
