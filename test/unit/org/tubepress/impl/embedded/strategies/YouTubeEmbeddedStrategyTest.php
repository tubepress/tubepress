<?php

require_once 'AbstractEmbeddedStrategyTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/embedded/strategies/YouTubeEmbeddedStrategy.class.php';

class org_tubepress_impl_embedded_YouTubeEmbeddedStrategyTest extends org_tubepress_impl_embedded_AbstractEmbeddedStrategyTest {
    
    function buildSut()
    {
        return new org_tubepress_impl_embedded_strategies_YouTubeEmbeddedStrategy();
    }
    
    function testCanHandle()
    {
        $this->getSut()->start();
        $this->assertTrue($this->getSut()->canHandle('youtube', 'two'));
    }
    
    function testCannotHandle()
    {
        $this->getSut()->start();
        $this->assertFalse($this->getSut()->canHandle('blabla', 'two'));
    }

    function getMock($className)
    {
        $mock = parent::getMock($className);
        if ($className === 'org_tubepress_api_http_AgentDetector') {
            $mock->expects($this->once())
                 ->method('isIphoneOrIpod')
                 ->will($this->returnValue(false));
        }
        return $mock;
    }
    
    function testExec()
    {
        $this->setOptions(array(
            org_tubepress_api_const_options_Embedded::PLAYER_COLOR => '777777',
            org_tubepress_api_const_options_Embedded::PLAYER_HIGHLIGHT => '111111',
            org_tubepress_api_const_options_Embedded::HIGH_QUALITY => true,
            org_tubepress_api_const_options_Embedded::LOOP => true,
            org_tubepress_api_const_options_Embedded::BORDER => true
        ));
        parent::testExec();
    }
  
    function expected()
    {
        return <<<EOT
<object type="application/x-shockwave-flash" data="http://www.youtube.com/v/videoid?color2=0x777777&amp;color1=0x111111&amp;rel=1&amp;autoplay=0&amp;loop=1&amp;egm=0&amp;border=1&amp;fs=1&amp;showinfo=0&amp;hd=1" style="width: 425px; height: 350px">
        <param name="wmode" value="transparent" />
        <param name="movie" value="http://www.youtube.com/v/videoid?color2=0x777777&amp;color1=0x111111&amp;rel=1&amp;autoplay=0&amp;loop=1&amp;egm=0&amp;border=1&amp;fs=1&amp;showinfo=0&amp;hd=1" />
        <param name="allowfullscreen" value="true" />
      </object>

EOT;
    }
    
}
?>
