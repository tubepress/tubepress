<?php

require_once BASE . '/sys/classes/org/tubepress/api/provider/Provider.class.php';
require_once BASE . '/sys/classes/org/tubepress/impl/feed/inspection/VimeoFeedInspectionCommand.class.php';

class org_tubepress_impl_feed_inspection_VimeoFeedInspectionCommandTest extends TubePressUnitTest {

    private $_sut;

    function setUp()
    {
        $this->_sut = new org_tubepress_impl_feed_inspection_VimeoFeedInspectionCommand();
    }

    function testCannotHandle()
    {
        $context = new stdClass();

        $context->providerName = org_tubepress_api_provider_Provider::YOUTUBE;
        $context->rawFeed      = 'something';

        $this->assertFalse($this->_sut->execute($context));
    }

    /**
     * @expectedException Exception
     */
    function testVimeoError()
    {
        $wrapper = new stdClass;
        $error   = new stdClass;

        $wrapper->stat = 'fail';
        $wrapper->err  = $error;
        $error->msg    = 'You failed';

        $context = new stdClass();

        $context->providerName = org_tubepress_api_provider_Provider::VIMEO;
        $context->rawFeed      = serialize($wrapper);

        $this->assertTrue($this->_sut->execute($context));
    }

    /**
     * @expectedException Exception
     */
    function testCannotUnserializeHandle()
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

