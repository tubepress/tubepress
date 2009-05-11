<?php
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/ioc/DefaultIocService.class.php';

class org_tubepress_ioc_DefaultIocServiceTest extends PHPUnit_Framework_TestCase {

	private $_sut;
    private $_expectedMapping;
	
	function setUp()
	{
		$this->_sut = new org_tubepress_ioc_DefaultIocService();
		$this->_expectedMapping = array(
		    org_tubepress_ioc_IocService::MESSAGE   => 'org_tubepress_message_WordPressMessageService',
		    org_tubepress_ioc_IocService::SHORTCODE => 'org_tubepress_shortcode_SimpleShortcodeService',
            org_tubepress_ioc_IocService::REFERENCE => 'org_tubepress_options_reference_SimpleOptionsReference',
            org_tubepress_ioc_IocService::FEED_INSP =>  'org_tubepress_gdata_inspection_SimpleFeedInspectionService',
            org_tubepress_ioc_IocService::CACHE => 'org_tubepress_cache_SimpleCacheService',
            org_tubepress_ioc_IocService::VID_FACT => 'org_tubepress_video_factory_SimpleVideoFactory',
            org_tubepress_ioc_IocService::QUERY_STR => 'org_tubepress_querystring_SimpleQueryStringService',
            org_tubepress_player_Player::YOUTUBE . "-player" => 'org_tubepress_player_impl_YouTubePlayer',
            org_tubepress_ioc_IocService::VALIDATION => 'org_tubepress_options_validation_SimpleInputValidationService',
            org_tubepress_ioc_IocService::FEED_RET => 'org_tubepress_gdata_retrieval_HTTPRequest2', 
            org_tubepress_ioc_IocService::URL_BUILDER => 'org_tubepress_url_SimpleUrlBuilder', 
            org_tubepress_embedded_EmbeddedPlayerService::YOUTUBE . '-embedded' => 'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
            org_tubepress_embedded_EmbeddedPlayerService::LONGTAIL . '-embedded' => 'org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService',
            org_tubepress_player_Player::NORMAL . "-player" => 'org_tubepress_player_impl_NormalPlayer',
            org_tubepress_player_Player::POPUP . "-player" => 'org_tubepress_player_impl_ModalPlayer',
            org_tubepress_player_Player::SHADOWBOX . "-player" => 'org_tubepress_player_impl_ModalPlayer',
            org_tubepress_player_Player::JQMODAL . "-player" => 'org_tubepress_player_impl_ModalPlayer',
            org_tubepress_player_Player::COLORBOX . "-player" => 'org_tubepress_player_impl_ModalPlayer',
            org_tubepress_ioc_IocService::STORAGE => 'org_tubepress_options_storage_WordPressStorageManager', 
            org_tubepress_ioc_IocService::THUMB => 'org_tubepress_thumbnail_SimpleThumbnailService', 
            org_tubepress_ioc_IocService::OPTIONS_MGR => 'org_tubepress_options_manager_SimpleOptionsManager', 
            org_tubepress_ioc_IocService::PAGINATION => 'org_tubepress_pagination_DiggStylePaginationService', 
            org_tubepress_ioc_IocService::W_PRINTER => 'org_tubepress_options_form_WidgetPrinter',
            org_tubepress_ioc_IocService::FORM_HNDLER => 'org_tubepress_options_form_FormHandler',
            org_tubepress_ioc_IocService::CAT_PRINTER => 'org_tubepress_options_form_CategoryPrinter',
            org_tubepress_ioc_IocService::GALLERY => 'org_tubepress_gallery_Gallery', 
            org_tubepress_ioc_IocService::WIDGET_GALL => 'org_tubepress_gallery_WidgetGallery'
		);
	}

	function testMapping()
	{
	    foreach ($this->_expectedMapping as $key => $value) {
	        $this->assertTrue(is_a($this->_sut->get($key), $value));
	    }
	}
}
?>