<?php

require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/api/provider/Provider.class.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/feed/inspectionstrategies/VimeoFeedInspectionStrategy.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';

class org_tubepress_impl_feed_inspectionstrategies_VimeoFeedInspectionStrategyTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        $this->_sut = new org_tubepress_impl_feed_inspectionstrategies_VimeoFeedInspectionStrategy();
    }

    function testCannotHandle()
    {
        $this->_sut->start();
        $this->assertFalse($this->_sut->canHandle(org_tubepress_api_provider_Provider::YOUTUBE, 'something'));
        $this->_sut->stop();
    }
    
    function testCanHandle()
    {
        $this->assertTrue($this->_sut->canHandle(org_tubepress_api_provider_Provider::VIMEO, 'something'));
    }
    
    function testCount()
    {
        $feed = $this->getSampleFeed();
        $result = $this->_sut->execute(org_tubepress_api_provider_Provider::VIMEO, $feed);
        $this->assertTrue(is_a($result, 'org_tubepress_api_feed_FeedResult'));
        $this->assertEquals(11, $result->getEffectiveTotalResultCount());
        $this->assertEquals(8, $result->getEffectiveDisplayCount());
    }

    function getSampleFeed()
    {
        return file_get_contents(dirname(__FILE__) . '/../../factory/feeds/vimeo.txt');
    }
}
?>
