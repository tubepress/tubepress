<?php

require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/api/provider/Provider.class.php';
require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/feed/commands/YouTubeFeedInspectionCommand.class.php';

class org_tubepress_impl_feed_commands_YouTubeFeedInspectionCommandTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        $this->_sut = new org_tubepress_impl_feed_commands_YouTubeFeedInspectionCommand();
    }

    function testCannotHandle()
    {
        $context = new stdClass();

        $context->providerName = org_tubepress_api_provider_Provider::VIMEO;
        $context->rawFeed      = 'something';

        $this->assertFalse($this->_sut->execute($context));
    }

    function testCanHandle()
    {
        $context = new stdClass();

        $context->providerName = org_tubepress_api_provider_Provider::YOUTUBE;
        $context->rawFeed      = $this->getSampleXml();

        $this->assertTrue($this->_sut->execute($context));
    }

    function testCount()
    {
        $context = new stdClass();

        $context->providerName = org_tubepress_api_provider_Provider::YOUTUBE;
        $context->rawFeed      = $this->getSampleXml();

        $this->assertTrue($this->_sut->execute($context));

        $this->_sut->execute($context);

        $result = $context->returnValue;
        $this->assertEquals(100, $result);
    }

    /**
     * @expectedException Exception
     */
    function testGetTotalResultNonNumeric()
    {
        $context = new stdClass();

        $context->providerName = org_tubepress_api_provider_Provider::YOUTUBE;
        $context->rawFeed      = str_replace("100", "fake", $this->getSampleXml());

        $this->assertTrue($this->_sut->execute($context));

        $this->_sut->execute($context);
    }

    /**
     * @expectedException Exception
     */
    function testGetTotalResultMissing()
    {
        $context = new stdClass();

        $context->providerName = org_tubepress_api_provider_Provider::YOUTUBE;
        $context->rawFeed      = str_replace("<openSearch:totalResults>100</openSearch:totalResults>", "", $this->getSampleXml());

        $this->assertTrue($this->_sut->execute($context));

        $this->_sut->execute($context);
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

