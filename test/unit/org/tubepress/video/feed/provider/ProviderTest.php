<?php

require_once dirname(__FILE__) . '/../../../../../../../test/unit/TubePressUnitTest.php';

class org_tubepress_video_feed_provider_ProviderTest extends TubePressUnitTest
{
    function setup()
    {
        org_tubepress_log_Log::setEnabled(false, array());
    }
    
    function testGetFeedResult()
    {
        $ioc = $this->getIoc();
        $insp = $ioc->get(org_tubepress_ioc_IocService::FEED_INSPECTION_YOUTUBE);
        $insp->expects($this->once())
                             ->method('getTotalResultCount')
                             ->with('xml')
                             ->will($this->returnValue(2));
        $ret = $ioc->get(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE);
        $insp->expects($this->once())
                             ->method('getQueryResultCount')
                             ->with('xml')
                             ->will($this->returnValue(1));
        $ret = $ioc->get(org_tubepress_ioc_IocService::FEED_RETRIEVAL_SERVICE);
        $ret->expects($this->once())
                           ->method('fetch')
                           ->will($this->returnValue('xml'));

        $result = org_tubepress_video_feed_provider_Provider::getFeedResult($ioc);
        $this->assertTrue(is_a($result, 'org_tubepress_video_feed_FeedResult'));
        $this->assertTrue($result->getEffectiveDisplayCount() === 1);
    }
}
?>
