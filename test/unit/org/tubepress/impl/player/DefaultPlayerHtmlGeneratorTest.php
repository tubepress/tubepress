<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/player/DefaultPlayerHtmlGenerator.class.php';

class org_tubepress_impl_player_DefaultPlayerHtmlGeneratorTest extends TubePressUnitTest {

    private $_sut;
    private static $_isMobile = false;

    function setUp()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_player_DefaultPlayerHtmlGenerator();
        $this->_video = $this->getMock('org_tubepress_api_video_Video');
        org_tubepress_impl_log_Log::setEnabled(false, array());
    }

    public function getMock($className)
    {
        $mock = parent::getMock($className);
        if ($className == 'org_tubepress_api_embedded_EmbeddedHtmlGenerator') {
            $mock->expects($this->once())
            ->method('getHtml')
            ->will($this->returnValue('foobar'));
        }
        return $mock;
    }

    function testMobile()
    {
        self::$_isMobile = true;
        $result = $this->_sut->getHtml($this->_video, 12);
        $this->assertEquals(<<<EOT
<div class="tubepress_normal_embedded_wrapper" style="width: 425px">
    <div id="tubepress_embedded_title_12" class="tubepress_embedded_title">
    </div>
    <div id="tubepress_embedded_object_12">
      foobar    
    </div>
  </div>

EOT
        , $result);
    }

    function testGetPreGalleryHtml()
    {
        self::$_isMobile = false;

        $this->_video->expects($this->once())
        ->method('getTitle')
        ->will($this->returnValue('sometitle'));

        $result = $this->_sut->getHtml($this->_video, 12);
        $expected = <<<EOT
<div class="tubepress_normal_embedded_wrapper" style="width: 425px">
    <div id="tubepress_embedded_title_12" class="tubepress_embedded_title">
      sometitle    
    </div>
    <div id="tubepress_embedded_object_12">
      foobar    
    </div>
  </div>

EOT;
        $this->assertEquals($expected, $result);
    }
}

