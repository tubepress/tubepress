<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/embedded/EmbeddedPlayerChain.class.php';
require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/api/provider/Provider.class.php';

class org_tubepress_impl_embedded_EmbeddedPlayerChainTest extends TubePressUnitTest {
    
    private $_sut;
    private $_context;
    
    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_embedded_EmbeddedPlayerChain();
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);
        
        if ($className === 'org_tubepress_api_provider_ProviderCalculator') {
            $mock->expects($this->once())
                 ->method('calculateProviderOfVideoId')
                 ->with($this->equalTo('videoid'))
                 ->will($this->returnValue(org_tubepress_api_provider_Provider::VIMEO));
        }
        
        if ($className === 'org_tubepress_api_patterns_cor_Chain') {
            
            $mock->expects($this->once())
                 ->method('execute')
                 ->with(
                     $this->anything(),
                     array(
                     'org_tubepress_impl_embedded_commands_JwFlvCommand',
                      'org_tubepress_impl_embedded_commands_YouTubeIframeCommand',
                      'org_tubepress_impl_embedded_commands_VimeoCommand'
                  ))
                  ->will($this->returnValue('foo'));
        }
        
        if ($className === 'org_tubepress_api_plugin_PluginManager') {
            $mock->expects($this->once())
                 ->method('runFilters')
                 ->with($this->equalTo(org_tubepress_api_const_plugin_FilterPoint::HTML_EMBEDDED), $this->anything())
                 ->will($this->returnValue('bar'));
        }
        
        return $mock;
    }
    
    function testGetHtml()
    {
        $result = $this->_sut->getHtml('videoid');
        $this->assertEquals('bar', $result);
    }
    
}
