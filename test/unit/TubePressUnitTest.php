<?php

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../classes/tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_options_manager_OptionsManager',
    'org_tubepress_ioc_IocService',
    'org_tubepress_options_reference_OptionsReference',
    'org_tubepress_message_MessageService',
    'org_tubepress_options_storage_StorageManager',
    'org_tubepress_url_impl_YouTubeUrlBuilder',
    'org_tubepress_video_feed_retrieval_HTTPRequest2',
    'org_tubepress_video_factory_impl_YouTubeVideoFactory',
    'org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService',
    'org_tubepress_video_feed_inspection_impl_YouTubeFeedInspectionService',
    'org_tubepress_cache_CacheService'));

class TubePressUnitTest extends PHPUnit_Framework_TestCase
{
    private $_needToInit = true;
    
    private $_ioc;
    
    private $_tpom;
    private $_msg;
    private $_tpsm;
    private $_urlBuilder;
    private $_feedRetrievalService;
    private $_youtubeVideoFactory;
    private $_youtubeEmbeddedImpl;
    private $_youtubeFeedInspectionService;
    private $_cacheService;
    
    private $options = array();

    function tearDown()
    {
        $this->_needToInit = true;
    }
    
    protected function getIoc()
    {
        if ($this->_needToInit) {
            $this->_init();
        }
        return $this->_ioc;
    }
    
    private function _init()
    {
        $this->_ioc  = $this->getMock('org_tubepress_ioc_IocService');
        $this->_tpom = $this->getMock('org_tubepress_options_manager_OptionsManager');
        $this->_msg  = $this->getMock('org_tubepress_message_MessageService');
        $this->_tpsm = $this->getMock('org_tubepress_options_storage_StorageManager');
        $this->_urlBuilder = $this->getMock('org_tubepress_url_impl_YouTubeUrlBuilder');
        $this->_youtubeVideoFactory = $this->getMock('org_tubepress_video_factory_impl_YouTubeVideoFactory');
        $this->_feedRetrievalService = $this->getMock('org_tubepress_video_feed_retrieval_HTTPRequest2');
        $this->_youtubeEmbeddedImpl = $this->getMock('org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService');
        $this->_youtubeFeedInspectionService = $this->getMock('org_tubepress_video_feed_inspection_impl_YouTubeFeedInspectionService');
        $this->_cacheService = $this->getMock('org_tubepress_cache_CacheService');
        
        $this->_ioc->expects($this->any())
                   ->method('get')
                   ->will($this->returnCallback(array($this, 'iocCallback')));
        $this->_tpom->expects($this->any())
                   ->method('get')
                   ->will($this->returnCallback(array($this, 'tpomCallback')));
        $this->_msg->expects($this->any())
                   ->method('_')
                   ->will($this->returnCallback(array($this, 'msgCallback')));
        $this->_tpsm->expects($this->any())
                    ->method('get')
                    ->will($this->returnCallback(array($this, 'msgCallback')));
    }
    
    function setOptions($options)
    {
        $this->options = array();
        foreach ($options as $key => $value) {
            $this->options[$key] = $value;
        }
    }
    
    function msgCallback()
    {
        $args = func_get_args();
        return $args[0];
    }
    
    function iocCallback()
    {
        $args = func_get_args();
        $vals = array(
           org_tubepress_ioc_IocService::OPTIONS_MANAGER => $this->_tpom,
           org_tubepress_ioc_IocService::MESSAGE_SERVICE => $this->_msg,
           org_tubepress_ioc_IocService::OPTIONS_STORAGE_MANAGER => $this->_tpsm,
           org_tubepress_ioc_IocService::URL_BUILDER_YOUTUBE => $this->_urlBuilder,
           org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE => $this->_feedRetrievalService,
           org_tubepress_ioc_IocService::VIDEO_FACTORY_YOUTUBE => $this->_youtubeVideoFactory,
           org_tubepress_ioc_IocService::EMBEDDED_IMPL_YOUTUBE => $this->_youtubeEmbeddedImpl,
           org_tubepress_ioc_IocService::FEED_INSPECTION_YOUTUBE => $this->_youtubeFeedInspectionService,
           org_tubepress_ioc_IocService::CACHE_SERVICE => $this->_cacheService,
        );
        return $vals[$args[0]];
    }
    
    function tpomCallback() {
        $args = func_get_args();
        
        if (array_key_exists($args[0], $this->options)) {
            return $this->options[$args[0]];
        }
        
        return org_tubepress_options_reference_OptionsReference::getDefaultValue($args[0]);
    }
    
    function fakeVideos()
    {
        return array(
            new org_tubepress_video_Video()
        );
    }
}
?>