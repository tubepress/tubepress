<?php

require_once 'AbstractCommandTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/embedded/commands/VimeoCommand.class.php';

class org_tubepress_impl_embedded_commands_VimeoCommandTest extends org_tubepress_impl_embedded_commands_AbstractCommandTest {
    
    function testCanHandleVimeo()
    {
        $context = new org_tubepress_impl_embedded_EmbeddedPlayerChainContext(org_tubepress_api_provider_Provider::VIMEO, 'videoid');
        
        TubePressChainTestUtils::assertCommandCanHandle($this->getSut(), $context);
    }
    
    function buildSut()
    {
        return new org_tubepress_impl_embedded_commands_VimeoCommand();
    }
    
    function expected()
    {
        return <<<EOT
<iframe src="http://player.vimeo.com/video/videoid?autoplay=0&amp;color=999999&amp;loop=0&amp;title=0&amp;byline=0&amp;portrait=0" width="425" height="350" frameborder="0"></iframe>

EOT;
    }

}

