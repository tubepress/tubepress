<?php

require_once 'AbstractEmbeddedStrategyTest.php';
require_once dirname(__FILE__) . '/../../../../../../../classes/org/tubepress/impl/embedded/strategies/YouTubeIframeEmbeddedStrategy.class.php';

class org_tubepress_impl_embedded_YouTubeIframeEmbeddedStrategyTest extends org_tubepress_impl_embedded_AbstractEmbeddedStrategyTest {
    
    function testCanHandleYouTubeIframe()
    {
	$this->setOptions(array(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL => 'youtube-iframe'));
        $this->getSut()->start();
        $canHandle = $this->getSut()->canHandle(org_tubepress_api_provider_Provider::YOUTUBE, 'videoid');
        
        $this->assertTrue($canHandle);
    }
    
    function buildSut()
    {
        return new org_tubepress_impl_embedded_strategies_YouTubeIframeEmbeddedStrategy();
    }
    
    function expected()
    {
        return <<<EOT
<iframe class="youtube-player" type="text/html" width="425" height="350" src="http://www.youtube.com/embed/videoid?rel=1&amp;autoplay=0&amp;loop=0&amp;border=0&amp;fs=1&amp;showinfo=0" frameborder="0"></iframe>

EOT;
    }

}
?>
