<?php

require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/api/provider/Provider.class.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/feed/commands/VimeoFeedInspectionCommand.class.php';

class org_tubepress_impl_feed_commands_VimeoFeedInspectionCommandTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        $this->_sut = new org_tubepress_impl_feed_commands_VimeoFeedInspectionCommand();
    }

    function testCannotHandle()
    {
        $context = new org_tubepress_impl_feed_FeedInspectorChainContext(org_tubepress_api_provider_Provider::YOUTUBE, 'something');
        TubePressChainTestUtils::assertCommandCannotHandle($this->_sut, $context);
    }
    
    function testCanHandle()
    {
        $context = new org_tubepress_impl_feed_FeedInspectorChainContext(org_tubepress_api_provider_Provider::VIMEO, 'something');
        TubePressChainTestUtils::assertCommandCanHandle($this->_sut, $context);
    }
    
    function testCount()
    {
        $feed = $this->getSampleFeed();
        $context = new org_tubepress_impl_feed_FeedInspectorChainContext(org_tubepress_api_provider_Provider::VIMEO, $feed);
        $this->_sut->execute($context);
        $result = $context->getReturnValue();
        $this->assertEquals(11, $result);
    }

    function getSampleFeed()
    {
        return file_get_contents(dirname(__FILE__) . '/../../factory/feeds/vimeo.txt');
    }
}

