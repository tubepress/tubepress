<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/embedded/impl/VimeoEmbeddedPlayerService.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_embedded_impl_VimeoEmbeddedPlayerServiceTest extends TubePressUnitTest {

    private $_sut;
    
    function setUp()
    {
        $this->_sut = new org_tubepress_embedded_impl_VimeoEmbeddedPlayerService();
    }
    
    function testToString()
    {
        $ioc = $this->getIoc();
        $tpom = $ioc->get(org_tubepress_ioc_IocService::OPTIONS_MANAGER);
        
        $tpom->expects($this->exactly(7))
             ->method('get')
             ->will($this->returnCallback(array('TubePressUnitTest', 'tpomCallback')));
             
        $this->assertEquals($this->expected(), $this->_sut->toString($ioc, 'FAKEID'));
    }
    
    function expected()
    {
        return <<<EOT
<object style="width: 450px; height:350px"
    data="http://vimeo.com/moogaloop.swf?clip_id=FAKEID&amp;fullscreen=1&amp;autoplay=0&amp;color=111111"
    type="application/x-shockwave-flash">
  <param name="allowfullscreen" value="1" />
  <param name="allowscriptaccess" value="always" />
  <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=FAKEID&amp;fullscreen=1&amp;autoplay=0&amp;color=111111" />
  <param name="wmode" value="transparent" />
</object>

EOT;
    }

}
?>