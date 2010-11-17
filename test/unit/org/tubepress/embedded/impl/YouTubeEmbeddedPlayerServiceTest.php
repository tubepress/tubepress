<?php

require_once dirname(__FILE__) . '/../../../../../../classes/org/tubepress/embedded/impl/YouTubeEmbeddedPlayerService.class.php';
require_once dirname(__FILE__) . '/../../../../TubePressUnitTest.php';

class org_tubepress_embedded_impl_YouTubeEmbeddedPlayerServiceTest extends TubePressUnitTest {
    
    private $_sut;
    
	function setUp()
	{
	    $this->initFakeIoc();
	    $this->_sut = new org_tubepress_embedded_impl_YouTubeEmbeddedPlayerService();
	}
	
    function testToString()
    {
        $this->setOptions(array(
            org_tubepress_api_const_options_Embedded::PLAYER_COLOR => '777777',
            org_tubepress_api_const_options_Embedded::PLAYER_HIGHLIGHT => '111111',
            org_tubepress_api_const_options_Embedded::HIGH_QUALITY => true,
            org_tubepress_api_const_options_Embedded::LOOP => true,
            org_tubepress_api_const_options_Embedded::BORDER => true
        ));
        $this->assertEquals($this->expected(), $this->_sut->toString('FAKEID'));
    }
    
    function expected()
    {
        return <<<EOT
<object type="application/x-shockwave-flash" data="http://www.youtube.com/v/FAKEID?color2=0x777777&amp;color1=0x111111&amp;rel=1&amp;autoplay=0&amp;loop=1&amp;egm=0&amp;border=1&amp;fs=1&amp;showinfo=0&amp;hd=1" style="width: 425px; height: 350px">
        <param name="wmode" value="transparent" />
        <param name="movie" value="http://www.youtube.com/v/FAKEID?color2=0x777777&amp;color1=0x111111&amp;rel=1&amp;autoplay=0&amp;loop=1&amp;egm=0&amp;border=1&amp;fs=1&amp;showinfo=0&amp;hd=1" />
        <param name="allowfullscreen" value="true" />
      </object>

EOT;
    }
	
}
?>
