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
        $this->assertEquals($this->expected(), $this->_sut->toString($this->getIoc(), 'FAKEID'));
    }
    
    function expected()
    {
        return <<<EOT
<object style="width: 425px; height:350px"
    data="http://vimeo.com/moogaloop.swf?clip_id=FAKEID&amp;fullscreen=1&amp;autoplay=0&amp;color=FFFFFF"
    type="application/x-shockwave-flash">
  <param name="allowfullscreen" value="1" />
  <param name="allowscriptaccess" value="always" />
  <param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=FAKEID&amp;fullscreen=1&amp;autoplay=0&amp;color=FFFFFF" />
  <param name="wmode" value="transparent" />
</object>

EOT;
    }

}
?>