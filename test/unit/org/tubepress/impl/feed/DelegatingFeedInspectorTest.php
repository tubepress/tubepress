<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/feed/DelegatingFeedInspector.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_impl_feed_DelegatingFeedInspectorTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_feed_DelegatingFeedInspector();
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);
        if ($className === 'org_tubepress_api_provider_ProviderCalculator') {
            $mock->expects($this->once())
                 ->method('calculateCurrentVideoProvider')
                 ->will($this->returnValue('providername'));
        }
        if ($className === 'org_tubepress_api_patterns_StrategyManager') {
            $mock->expects($this->once())
                 ->method('executeStrategy')
                 ->with(array(
                     'org_tubepress_impl_feed_inspectionstrategies_YouTubeFeedInspectionStrategy', 
                     'org_tubepress_impl_feed_inspectionstrategies_VimeoFeedInspectionStrategy'
                  ), new PHPUnit_Framework_Constraint_IsEqual('providername'),
                     new PHPUnit_Framework_Constraint_IsEqual('rawfeed'))
                  ->will($this->returnValue('foo'));
        }
        return $mock;
    }

    function testCount()
    {
        $this->assertEquals('foo', $this->_sut->count('rawfeed'));
    }
}
?>
