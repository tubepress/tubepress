<?php

require_once dirname(__FILE__) . '/../../../../../classes/org/tubepress/embedded/impl/YouTubeEmbeddedPlayerService.class.php';
require_once 'AbstractEmbeddedPlayerServiceTest.php';

class org_tubepress_embedded_impl_YouTubeEmbeddedPlayerServiceTest extends org_tubepress_embedded_impl_AbstractEmbeddedPlayerServiceTest {
    
	function setUp()
	{
	    parent::parentSetUp(new org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService(), 12);
	}
	
	function testToString()
	{
		$link = "http://www.youtube.com/v/FAKEID&amp;color2=0x777777&amp;color1=0x111111&amp;rel=1&amp;autoplay=0&amp;loop=1&amp;egm=0&amp;border=1&amp;fs=1&amp;showinfo=0";
		
		$this->assertEquals(<<<EOT
<object type="application/x-shockwave-flash" data="$link" style="width: 450px; height: 350px">
    <param name="wmode" value="transparent" />
    <param name="movie" value="$link" />
    <param name="allowfullscreen" value="true" />
</object>
EOT
			,  $this->_sut->toString('FAKEID'));
	}
}
?>