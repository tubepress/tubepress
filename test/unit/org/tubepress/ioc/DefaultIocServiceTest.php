<?php
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/ioc/DefaultIocService.class.php';

function get_option() { }
function update_option() { }

class org_tubepress_ioc_DefaultIocServiceTest extends PHPUnit_Framework_TestCase {

    private $_sut;
    private $_expectedMapping;
    
    function setUp()
    {
        $this->_sut = new org_tubepress_ioc_DefaultIocService();
        $this->_expectedMapping = array(
            org_tubepress_ioc_IocService::CACHE_SERVICE => 'org_tubepress_cache_SimpleCacheService',
            org_tubepress_ioc_IocService::EMBEDDED_IMPL_YOUTUBE => 'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
            org_tubepress_ioc_IocService::EMBEDDED_IMPL_VIMEO => 'org_tubepress_embedded_impl_VimeoEmbeddedPlayerService',
            org_tubepress_ioc_IocService::EMBEDDED_IMPL_LONGTAIL => 'org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService',
            org_tubepress_ioc_IocService::MESSAGE_SERVICE => 'org_tubepress_message_WordPressMessageService',
            org_tubepress_ioc_IocService::OPTIONS_MANAGER => 'org_tubepress_options_manager_SimpleOptionsManager',    
            org_tubepress_ioc_IocService::OPTIONS_STORAGE_MANAGER => 'org_tubepress_options_storage_WordPressStorageManager',    
            org_tubepress_ioc_IocService::PAGINATION_SERVICE => 'org_tubepress_pagination_DiggStylePaginationService',    
            //single video
            org_tubepress_ioc_IocService::URL_BUILDER_YOUTUBE => 'org_tubepress_url_impl_YouTubeUrlBuilder',
            org_tubepress_ioc_IocService::URL_BUILDER_VIMEO => 'org_tubepress_url_impl_VimeoUrlBuilder',
            org_tubepress_ioc_IocService::VIDEO_FACTORY_YOUTUBE => 'org_tubepress_video_factory_impl_YouTubeVideoFactory',
            org_tubepress_ioc_IocService::VIDEO_FACTORY_VIMEO => 'org_tubepress_video_factory_impl_VimeoVideoFactory',
            org_tubepress_ioc_IocService::VIDEO_FACTORY_LOCAL => 'org_tubepress_video_factory_impl_LocalVideoFactory',
            org_tubepress_ioc_IocService::FEED_INSPECTION_YOUTUBE => 'org_tubepress_video_feed_inspection_impl_YouTubeFeedInspectionService',
            org_tubepress_ioc_IocService::FEED_INSPECTION_VIMEO => 'org_tubepress_video_feed_inspection_impl_VimeoFeedInspectionService',
            org_tubepress_ioc_IocService::FEED_INSPECTION_LOCAL => 'org_tubepress_video_feed_inspection_impl_LocalFeedInspectionService',
            org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE => 'org_tubepress_video_feed_retrieval_HTTPRequest2'
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
