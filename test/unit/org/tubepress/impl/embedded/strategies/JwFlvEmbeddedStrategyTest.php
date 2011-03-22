<?php

require_once 'AbstractEmbeddedStrategyTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/embedded/strategies/JwFlvEmbeddedStrategy.class.php';

class org_tubepress_impl_embedded_JwFlvEmbeddedStrategyTest extends org_tubepress_impl_embedded_AbstractEmbeddedStrategyTest {
    
    function testCanHandleVimeoWithLongtail()
    {
        $this->setOptions(array(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL => org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL));
        $this->getSut()->start();
        $canHandle = $this->getSut()->canHandle(org_tubepress_api_provider_Provider::VIMEO, 'videoid');
        
        $this->assertFalse($canHandle);
    }
    
    function testCanHandleYouTubeWithLongtail()
    {
        $this->setOptions(array(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL => org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL));
        $this->getSut()->start();
        $canHandle = $this->getSut()->canHandle(org_tubepress_api_provider_Provider::YOUTUBE, 'videoid');
        
        $this->assertTrue($canHandle);
    }
    
    function testCanHandleYouTubeWithDefaultPlayer()
    {
        $this->setOptions(array(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL => org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED));
        $this->getSut()->start();
        $canHandle = $this->getSut()->canHandle(org_tubepress_api_provider_Provider::YOUTUBE, 'videoid');
        
        $this->assertFalse($canHandle);
    }
 
    
    function expected()
    {
        return <<<EOT
<object type="application/x-shockwave-flash" data="tubepress_base_url/sys/ui/static/flash/longtail/player.swf" style="width: 425px; height: 350px" >
        <param name="AllowScriptAccess" value="never" />
        <param name="wmode" value="opaque" />
        <param name="movie" value="tubepress_base_url/sys/ui/static/flash/longtail/player.swf" />
        <param name="bgcolor" value="999999" />
        <param name="frontcolor" value="FFFFFF" />
        <param name="quality" value="high" />
        <param name="flashvars" value="file=http://www.youtube.com/watch?v=videoid&amp;autostart=false&amp;height=350&amp;width=425&amp;frontcolor=FFFFFF" />
      </object>

EOT;
    }
    
    function buildSut()
    {
        return new org_tubepress_impl_embedded_strategies_JwFlvEmbeddedStrategy();
    }
    
}
?>
