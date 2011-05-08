<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/embedded/DelegatingEmbeddedPlayer.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_impl_embedded_DelegatingEmbeddedPlayerTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_embedded_DelegatingEmbeddedPlayer();
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
        if ($className === 'org_tubepress_api_patterns_StrategyManager') {
            $mock->expects($this->once())
                 ->method('executeStrategy')
                 ->with(array(
                     'org_tubepress_impl_embedded_strategies_JwFlvEmbeddedStrategy',
                      'org_tubepress_impl_embedded_strategies_YouTubeIframeEmbeddedStrategy',
                      'org_tubepress_impl_embedded_strategies_VimeoEmbeddedStrategy'
                  ), new PHPUnit_Framework_Constraint_IsEqual(org_tubepress_api_provider_Provider::VIMEO),
                     new PHPUnit_Framework_Constraint_IsEqual('videoid'))
                  ->will($this->returnValue('foo'));
        }
        
        return $mock;
    }
    
    function testToString()
    {
        $this->assertEquals('foo', $this->_sut->toString('videoid'));
    }
    
}
