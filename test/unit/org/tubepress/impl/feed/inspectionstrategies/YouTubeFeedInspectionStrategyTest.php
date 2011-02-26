<?php

require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/api/provider/Provider.class.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/feed/inspectionstrategies/YouTubeFeedInspectionStrategy.class.php';
require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';

class org_tubepress_impl_feed_inspectionstrategies_YouTubeFeedInspectionStrategyTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        $this->_sut = new org_tubepress_impl_feed_inspectionstrategies_YouTubeFeedInspectionStrategy();
    }

    function testCannotHandle()
    {
        $this->_sut->start();
        $this->assertFalse($this->_sut->canHandle(org_tubepress_api_provider_Provider::VIMEO, 'something'));
        $this->_sut->stop();
    }
    
    function testCanHandle()
    {
        $this->assertTrue($this->_sut->canHandle(org_tubepress_api_provider_Provider::YOUTUBE, 'something'));
    }
    
    function testCount()
    {
        $feed = $this->getSampleXml();
        $result = $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, $feed);
        $this->assertEquals(100, $result);
    }
    
    /**
     * @expectedException Exception
     */
    function testGetTotalResultNonNumeric()
    {
        $feed = str_replace("100", "fake", $this->getSampleXml());
        $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, $feed);
    }
    
    /**
     * @expectedException Exception
     */
    function testGetTotalResultMissing()
    {
        $feed = str_replace("<openSearch:totalResults>100</openSearch:totalResults>", "", $this->getSampleXml());
        $this->_sut->execute(org_tubepress_api_provider_Provider::YOUTUBE, $feed);
    }

    function getSampleXml()
    {
        return <<<EOT
<xml version='1.0' encoding='UTF-8'>
    <feed xmlns='http://www.w3.org/2005/Atom'
        xmlns:openSearch='http://a9.com/-/spec/opensearch/1.1/'>
        <openSearch:totalResults>100</openSearch:totalResults>
        <entry /><entry /><entry />
    </feed>
</xml>
EOT;
    }
}
?>
