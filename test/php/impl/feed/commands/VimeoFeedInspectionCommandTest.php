<?php

require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/api/provider/Provider.class.php';
require_once dirname(__FILE__) . '/../../../../../sys/classes/org/tubepress/impl/feed/commands/VimeoFeedInspectionCommand.class.php';

class org_tubepress_impl_feed_commands_VimeoFeedInspectionCommandTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        $this->_sut = new org_tubepress_impl_feed_commands_VimeoFeedInspectionCommand();
    }

    function testCannotHandle()
    {
        $context = new stdClass();

        $context->providerName = org_tubepress_api_provider_Provider::YOUTUBE;
        $context->rawFeed      = 'something';

        $this->assertFalse($this->_sut->execute($context));
    }

    function testCanHandle()
    {
        $context = new stdClass();

        $context->providerName = org_tubepress_api_provider_Provider::VIMEO;
        $context->rawFeed      = 'something';

        $this->assertTrue($this->_sut->execute($context));
    }

    function testCount()
    {
        $context = new stdClass();

        $context->providerName = org_tubepress_api_provider_Provider::VIMEO;
        $context->rawFeed      = $this->getSampleFeed();

        $this->assertTrue($this->_sut->execute($context));

        $result = $context->returnValue;
        $this->assertEquals(11, $result);
    }

    function getSampleFeed()
    {
        return file_get_contents(dirname(__FILE__) . '/../../factory/feeds/vimeo.txt');
    }
}

