<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/embedded/impl/JwFlvEmbeddedPlayerService.class.php';
require_once 'AbstractEmbeddedPlayerServiceTest.php';

class org_tubepress_embedded_impl_JwFlvEmbeddedPlayerServiceTest extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerServiceTest {
    
	function setUp()
	{
	    parent::parentSetUp(new org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService(), 3);
	}
	
	function testToString()
	{
		$link = "http://www.youtube.com/v/FAKEID&amp;color2=0x777777&amp;color1=0x111111&amp;rel=1&amp;autoplay=0&amp;loop=1&amp;egm=0&amp;border=1&amp;fs=1&amp;showinfo=0";
		
		$this->assertEquals(<<<EOT
<object type="application/x-shockwave-flash" data="/ui/embedded/longtail/lib/player.swf" style="width: 450px; height: 350px" >
    <param name="AllowScriptAccess" value="never" />
    <param name="wmode" value="transparent" />
    <param name="movie" value="/ui/embedded/longtail/lib/player.swf" />
    <param name="bgcolor" value="#000000" />
    <param name="quality" value="high" />
    <param name="flashvars" value="file=http://www.youtube.com/watch?v=FAKEID&amp;autostart=false&amp;height=350&amp;width=450" />
</object>
EOT
			,  $this->_sut->toString('FAKEID'));
	}
}
?>