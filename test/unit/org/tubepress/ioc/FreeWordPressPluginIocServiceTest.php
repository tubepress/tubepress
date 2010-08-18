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
            'org_tubepress_browser_BrowserDetector'                     => 'org_tubepress_browser_MobileEspBrowserDetector',
            'org_tubepress_cache_CacheService'                          => 'org_tubepress_cache_PearCacheLiteCacheService',
            'org_tubepress_embedded_EmbeddedPlayerService'              => 'org_tubepress_embedded_impl_DelegatingEmbeddedPlayerService',
            'org_tubepress_message_MessageService'                      => 'org_tubepress_message_impl_WordPressMessageService',
            'org_tubepress_options_manager_OptionsManager'              => 'org_tubepress_options_manager_SimpleOptionsManager',    
            'org_tubepress_options_storage_StorageManager'              => 'org_tubepress_options_storage_WordPressStorageManager',    
            'org_tubepress_pagination_PaginationService'                => 'org_tubepress_pagination_DiggStylePaginationService',    
            'org_tubepress_url_UrlBuilder'                              => 'org_tubepress_url_impl_YouTubeUrlBuilder',
            'org_tubepress_video_factory_VideoFactory'                  => 'org_tubepress_video_factory_impl_YouTubeVideoFactory',
            'org_tubepress_video_feed_inspection_FeedInspectionService' => 'org_tubepress_video_feed_inspection_impl_YouTubeFeedInspectionService',
            'org_tubepress_video_feed_retrieval_FeedRetrievalService'   => 'org_tubepress_video_feed_retrieval_HTTPRequest2'
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
