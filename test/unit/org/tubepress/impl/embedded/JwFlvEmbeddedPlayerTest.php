<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/impl/embedded/JwFlvEmbeddedPlayer.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_impl_embedded_JwFlvEmbeddedPlayerTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_impl_embedded_JwFlvEmbeddedPlayer();
    }
    
    function testToString()
    {
        $this->assertEquals($this->expected(), $this->_sut->toString('FAKEID'));
    }
    
    function expected()
    {
        return <<<EOT
<object type="application/x-shockwave-flash" data="/ui/lib/embedded_flash/longtail/lib/player.swf" style="width: 425px; height: 350px" >
        <param name="AllowScriptAccess" value="never" />
        <param name="wmode" value="opaque" />
        <param name="movie" value="/ui/lib/embedded_flash/longtail/lib/player.swf" />
        <param name="bgcolor" value="999999" />
        <param name="frontcolor" value="FFFFFF" />
        <param name="quality" value="high" />
        <param name="flashvars" value="file=http://www.youtube.com/watch?v=FAKEID&amp;autostart=false&amp;height=350&amp;width=425&amp;frontcolor=FFFFFF" />
      </object>

EOT;
    }
    
}
?>
