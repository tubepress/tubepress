<?php

require_once BASE . '/sys/classes/org/tubepress/impl/feed/FeedInspectorChain.class.php';

class org_tubepress_impl_feed_FeedInspectorChainTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_feed_FeedInspectorChain();
    }

    function testCountCouldNotHandle()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc           = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('videoProvider');

        $mockChainContext = \Mockery::mock('stdClass');
        $mockChainContext->returnValue = 'foobar';

        $chain = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $chain->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $chain->shouldReceive('execute')->once()->with($mockChainContext, array(
    			'org_tubepress_impl_feed_inspection_YouTubeFeedInspectionCommand',
    			'org_tubepress_impl_feed_inspection_VimeoFeedInspectionCommand'
        ))->andReturn(false);

        $this->assertEquals(0, $this->_sut->getTotalResultCount('rawfeed'));
    }

    function testCount()
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();

        $pc           = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $pc->shouldReceive('calculateCurrentVideoProvider')->once()->andReturn('videoProvider');

        $mockChainContext = \Mockery::mock('stdClass');
        $mockChainContext->returnValue = 'foobar';

        $chain = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $chain->shouldReceive('createContextInstance')->once()->andReturn($mockChainContext);
        $chain->shouldReceive('execute')->once()->with($mockChainContext, array(
			'org_tubepress_impl_feed_inspection_YouTubeFeedInspectionCommand',
			'org_tubepress_impl_feed_inspection_VimeoFeedInspectionCommand'
		))->andReturn(true);

        $this->assertEquals('foobar', $this->_sut->getTotalResultCount('rawfeed'));
    }
}
