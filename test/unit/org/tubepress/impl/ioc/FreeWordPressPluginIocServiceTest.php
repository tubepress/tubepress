<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/ioc/FreeWordPressPluginIocService.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';


class org_tubepress_impl_ioc_FreeWordPressPluginIocServiceTest extends TubePressUnitTest {

    private $_sut;
    private $_expectedMapping;
    
    function setUp()
    {
        $this->_sut = new org_tubepress_impl_ioc_FreeWordPressPluginIocService();
        $this->_expectedMapping = array(
        
        'org_tubepress_api_bootstrap_Bootstrapper'         => 'org_tubepress_impl_bootstrap_FreeWordPressPluginBootstrapper',
        'org_tubepress_api_cache_Cache'                    => 'org_tubepress_impl_cache_PearCacheLiteCacheService',
        'org_tubepress_api_embedded_EmbeddedPlayer'        => 'org_tubepress_impl_embedded_DelegatingEmbeddedPlayer',
        'org_tubepress_api_environment_Detector'           => 'org_tubepress_impl_environment_SimpleEnvironmentDetector',
        'org_tubepress_api_factory_VideoFactory'           => 'org_tubepress_impl_factory_DelegatingVideoFactory',
        'org_tubepress_api_feed_FeedFetcher'               => 'org_tubepress_impl_feed_CacheAwareFeedFetcher',
        'org_tubepress_api_feed_FeedInspector'             => 'org_tubepress_impl_feed_DelegatingFeedInspector',
        'org_tubepress_api_filesystem_Explorer'            => 'org_tubepress_impl_filesystem_FsExplorer',
        'org_tubepress_api_html_HtmlGenerator'             => 'org_tubepress_impl_html_DefaultHtmlGenerator',
        'org_tubepress_api_http_HttpClient'                => 'org_tubepress_impl_http_FastHttpClient',
        'org_tubepress_api_message_MessageService'         => 'org_tubepress_impl_message_WordPressMessageService',
        'org_tubepress_api_options_OptionsManager'         => 'org_tubepress_impl_options_SimpleOptionsManager',    
        'org_tubepress_api_options_OptionValidator'        => 'org_tubepress_impl_options_SimpleOptionValidator',    
        'org_tubepress_api_options_StorageManager'         => 'org_tubepress_impl_options_WordPressStorageManager',
        'org_tubepress_api_pagination_Pagination'          => 'org_tubepress_impl_pagination_DiggStylePaginationService',
        'org_tubepress_api_plugin_PluginManager'           => 'org_tubepress_impl_plugin_PluginManagerImpl',
        'org_tubepress_api_patterns_StrategyManager'       => 'org_tubepress_impl_patterns_StrategyManagerImpl',
        'org_tubepress_api_player_Player'                  => 'org_tubepress_impl_player_SimplePlayer',
        'org_tubepress_api_provider_Provider'              => 'org_tubepress_impl_provider_SimpleProvider',
        'org_tubepress_api_provider_ProviderCalculator'    => 'org_tubepress_impl_provider_SimpleProviderCalculator',
        'org_tubepress_api_querystring_QueryStringService' => 'org_tubepress_impl_querystring_SimpleQueryStringService',
        'org_tubepress_api_shortcode_ShortcodeParser'      => 'org_tubepress_impl_shortcode_SimpleShortcodeParser',
        'org_tubepress_api_single_SingleVideo'             => 'org_tubepress_impl_single_SimpleSingleVideo',
        'org_tubepress_api_theme_ThemeHandler'             => 'org_tubepress_impl_theme_SimpleThemeHandler',
        'org_tubepress_api_url_UrlBuilder'                 => 'org_tubepress_impl_url_DelegatingUrlBuilder');
    }

    /**
     * @expectedException Exception
     */
    function testNoBindCall()
    {
        $this->_sut->to('fa');
    }
    
    /**
     * @expectedException Exception
     */
    function testNeverBound()
    {
        $this->_sut->get('something');
    }
    
    function testGetTwice()
    {
        $result = $this->_sut->get('org_tubepress_impl_ioc_FreeWordPressPluginIocServiceTest');
        $this->assertNotNull($result);
        $result2 = $this->_sut->get('org_tubepress_impl_ioc_FreeWordPressPluginIocServiceTest');
        $this->assertEquals($result, $result2);
    } 
    
    function testSingleton()
    {
        $this->assertNotNull($this->_sut->get('org_tubepress_impl_ioc_FreeWordPressPluginIocServiceTest'));
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
