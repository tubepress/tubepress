<?php

require_once 'AbstractCommandTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/embedded/commands/YouTubeIframeCommand.class.php';

class org_tubepress_impl_embedded_commands_YouTubeIframeCommandTest extends org_tubepress_impl_embedded_commands_AbstractCommandTest {
    
    function testCanHandleYouTubeIframe()
    {
        $context = new org_tubepress_impl_embedded_EmbeddedPlayerChainContext(org_tubepress_api_provider_Provider::YOUTUBE, 'videoid');
        
        TubePressChainTestUtils::assertCommandCanHandle($this->getSut(), $context);
    }
    
    function buildSut()
    {
        return new org_tubepress_impl_embedded_commands_YouTubeIframeCommand();
    }
    
    function expected()
    {
        return <<<EOT
<iframe class="youtube-player" type="text/html" width="425" height="350" src="http://www.youtube.com/embed/videoid?rel=1&amp;autoplay=0&amp;loop=0&amp;fs=1&amp;showinfo=0&amp;wmode=transparent" frameborder="0"></iframe>

EOT;
    }

}
