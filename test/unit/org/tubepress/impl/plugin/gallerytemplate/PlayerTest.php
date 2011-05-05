<?php

require_once dirname(__FILE__) . '/../../../../../TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/plugin/gallerytemplate/Player.class.php';
class_exists('org_tubepress_api_video_Video') || require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/api/video/Video.class.php';

class org_tubepress_impl_plugin_gallerytemplate_PlayerTest extends TubePressUnitTest
{
    private $_sut;

    function setup()
    {
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_plugin_gallerytemplate_Player();
    }

    function testPlayerAboveAndBelow()
    {
        $fakeTemplate = $this->getMock('org_tubepress_api_template_Template');
        $feedResult = $this->getMock('org_tubepress_api_provider_ProviderResult');
        $feedResult->expects($this->once())
                   ->method('getVideoArray')
                   ->will($this->returnValue(array(new org_tubepress_api_video_Video())));
        $this->_sut->alter_galleryTemplate($fakeTemplate, $feedResult, 3);
    }
}
