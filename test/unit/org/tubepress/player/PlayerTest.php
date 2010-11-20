<?php

require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/player/SimplePlayer.class.php';

class org_tubepress_api_player_PlayerTest extends TubePressUnitTest {
    
    private $_sut;
	private static $_isMobile = false;
    
    function setUp()
    {
	$this->initFakeIoc();
	$this->_sut = new org_tubepress_player_SimplePlayer();
        $this->_video = $this->getMock('org_tubepress_api_video_Video');
        org_tubepress_util_Log::setEnabled(false, array());
    }

	public function getMock($className)
	{
		$mock = parent::getMock($className);
		if ($className == 'org_tubepress_api_embedded_EmbeddedPlayer') {
			$mock->expects($this->once())
				->method('toString')
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
?>
