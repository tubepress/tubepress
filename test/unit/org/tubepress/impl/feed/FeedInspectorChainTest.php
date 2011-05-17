<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/feed/FeedInspectorChain.class.php';

class org_tubepress_impl_feed_FeedInspectorChainTest extends TubePressUnitTest {
    
    private $_sut;
    private $_result;
    
    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_feed_FeedInspectorChain();
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);
        if ($className === 'org_tubepress_api_provider_ProviderCalculator') {
            $mock->expects($this->once())
                 ->method('calculateCurrentVideoProvider')
                 ->will($this->returnValue('providername'));
        }
        if ($className === 'org_tubepress_api_patterns_cor_Chain') {
            $mock->expects($this->once())
                 ->method('execute')
                 ->with($this->anything(), array(
            'org_tubepress_impl_feed_commands_YouTubeFeedInspectionCommand',
            'org_tubepress_impl_feed_commands_VimeoFeedInspectionCommand'
        ))
                  ->will($this->returnCallback(array($this, 'fake')));
        }
        return $mock;
    }

    function testCount()
    {
        $this->_sut->getTotalResultCount('rawfeed');
        $this->assertEquals('foo', $this->_result);
    }
    
    function fake()
    {
        $this->_result = 'foo';
    }
}
