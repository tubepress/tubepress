<?php

function_exists('tubepress_load_classes')
    || require(dirname(__FILE__) . '/../../../tubepress_classloader.php');
tubepress_load_classes(array('org_tubepress_embedded_impl_JwFlvEmbeddedPlayerService'));

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
<object 
    type="application/x-shockwave-flash" 
    data="/ui/embedded/longtail/lib/player.swf"
    width="425" 
    height="355" 
    id="VideoPlayback">
    <param name="movie" value="/ui/embedded/longtail/lib/player.swf" />
    <param name="allowscriptacess" value="sameDomain" />
    <param name="bgcolor" value="#000000" />
    <param name="quality" value="high" />
    <param name="flashvars" value="file=http://www.youtube.com/watch?v=FAKEID&autostart=false&height=350&width=450" />
</object>
EOT
			,  $this->_sut->toString('FAKEID'));
	}
}
?>