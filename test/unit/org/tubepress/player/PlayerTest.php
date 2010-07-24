<?php

require_once dirname(__FILE__) . '/../../../../../test/unit/TubePressUnitTest.php';
require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/player/Player.class.php';

class org_tubepress_player_PlayerTest extends TubePressUnitTest {
    
    private $_video;
    
    function setUp()
    {
        $this->_video = $this->getMock('org_tubepress_video_Video');
        org_tubepress_log_Log::setEnabled(false, array());
    }
    
    function testGetPreGalleryHtml()
    {
        $this->_video->expects($this->once())
                     ->method('getId')
                     ->will($this->returnValue('TTER'));
        
        org_tubepress_player_Player::getHtml($this->getIoc(), $this->_video, 12);
    }
}
?>