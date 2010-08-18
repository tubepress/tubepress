<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/embedded/impl/JwFlvEmbeddedPlayerService.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_embedded_impl_JwFlvEmbeddedPlayerServiceTest extends TubePressUnitTest {
    
    private $_sut;
    
    function setUp()
    {
        $this->initFakeIoc();
        $this->_sut = new org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService();
    }
    
    function testToString()
    {
        $this->assertEquals($this->expected(), $this->_sut->toString('FAKEID'));
    }
    
    function expected()
    {
        return <<<EOT
<object type="application/x-shockwave-flash" data="/ui/embedded_flash/longtail/lib/player.swf" style="width: 425px; height: 350px" >
        <param name="AllowScriptAccess" value="never" />
        <param name="wmode" value="transparent" />
        <param name="movie" value="/ui/embedded_flash/longtail/lib/player.swf" />
        <param name="bgcolor" value="#000000" />
        <param name="quality" value="high" />
        <param name="flashvars" value="file=http://www.youtube.com/watch?v=FAKEID&amp;autostart=false&amp;height=350&amp;width=425" />
      </object>
EOT;
    }
    
}
?>