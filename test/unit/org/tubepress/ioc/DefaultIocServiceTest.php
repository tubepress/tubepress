<?php
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/ioc/DefaultIocService.class.php';

class org_tubepress_ioc_DefaultIocServiceTest extends PHPUnit_Framework_TestCase {

	private $_sut;
    private $_expectedMapping;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_ioc_DefaultIocService();
		$this->_expectedMapping = array(
		
		    // 0 SETTERS
		    org_tubepress_ioc_IocService::MESSAGE_SERVICE               => 'org_tubepress_message_WordPressMessageService',
            org_tubepress_ioc_IocService::OPTIONS_REFERENCE             => 'org_tubepress_options_reference_SimpleOptionsReference',
            org_tubepress_ioc_IocService::FEED_INSPECTION_SERVICE       => 'org_tubepress_video_feed_inspection_YouTubeFeedInspectionService',
            org_tubepress_ioc_IocService::QUERY_STRING_SERVICE          => 'org_tubepress_querystring_SimpleQueryStringService',
            org_tubepress_ioc_IocService::LOG                           => 'org_tubepress_log_LogImpl',
            org_tubepress_ioc_IocService::BROWSER_DETECTOR              => 'org_tubepress_browser_BrowserDetectorImpl',
            
            // 1 SETTER
            org_tubepress_ioc_IocService::URL_BUILDER                           => 'org_tubepress_url_YouTubeUrlBuilder', 
            org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE . '-embedded' => 'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
            org_tubepress_player_Player::YOUTUBE . "-player"                    => 'org_tubepress_player_impl_YouTubePlayer',
            org_tubepress_ioc_IocService::GALLERY_TEMPLATE                      => 'org_tubepress_template_SimpleTemplate',
            org_tubepress_ioc_IocService::YOUTUBE_EMBEDDED_TEMPLATE             => 'org_tubepress_template_SimpleTemplate',
            org_tubepress_ioc_IocService::LONGTAIL_EMBEDDED_TEMPLATE            => 'org_tubepress_template_SimpleTemplate',
            org_tubepress_ioc_IocService::NORMAL_PLAYER_TEMPLATE                => 'org_tubepress_template_SimpleTemplate',
            org_tubepress_ioc_IocService::MODAL_PLAYER_TEMPLATE                 => 'org_tubepress_template_SimpleTemplate',
            org_tubepress_ioc_IocService::CACHE_SERVICE                         => 'org_tubepress_cache_SimpleCacheService',
            org_tubepress_ioc_IocService::OPTIONS_FORM_TEMPLATE                 => 'org_tubepress_template_SimpleTemplate',
            
            // 2 SETTERS
            org_tubepress_ioc_IocService::STORAGE_MANAGER                        => 'org_tubepress_options_storage_WordPressStorageManager', 
            org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE                 => 'org_tubepress_video_feed_retrieval_HTTPRequest2', 
            org_tubepress_embedded_EmbeddedPlayerService::LONGTAIL . '-embedded' => 'org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService',
            org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE . '-embedded'  => 'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
            org_tubepress_ioc_IocService::SHORTCODE_SERVICE                      => 'org_tubepress_shortcode_SimpleShortcodeService',
            org_tubepress_ioc_IocService::VIDEO_FACTORY                          => 'org_tubepress_video_factory_YouTubeVideoFactory',
            org_tubepress_player_Player::NORMAL . "-player"                      => 'org_tubepress_player_impl_NormalPlayer',
            org_tubepress_player_Player::STATICC . "-player"                     => 'org_tubepress_player_impl_NormalPlayer',
            org_tubepress_player_Player::POPUP . "-player"                       => 'org_tubepress_player_impl_ModalPlayer',
            org_tubepress_player_Player::SHADOWBOX . "-player"                   => 'org_tubepress_player_impl_ModalPlayer',
            org_tubepress_player_Player::JQMODAL . "-player"                     => 'org_tubepress_player_impl_ModalPlayer',
            
            // 3 SETTERS
            org_tubepress_ioc_IocService::OPTIONS_MANAGER               => 'org_tubepress_options_manager_SimpleOptionsManager',
            org_tubepress_ioc_IocService::PAGINATION_SERVICE            => 'org_tubepress_pagination_DiggStylePaginationService', 
            org_tubepress_ioc_IocService::VALIDATION_SERVICE            => 'org_tubepress_options_validation_SimpleInputValidationService',
            
            // 4+ SETTERS            
            org_tubepress_ioc_IocService::OPTIONS_FORM_HANDLER          => 'org_tubepress_options_form_FormHandler',
            org_tubepress_ioc_IocService::VIDEO_PROVIDER                => 'org_tubepress_video_feed_provider_ProviderImpl',
            org_tubepress_ioc_IocService::GALLERY                       => 'org_tubepress_gallery_TubePressGalleryImpl'
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
