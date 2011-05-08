<?php

require_once 'AbstractEmbeddedStrategyTest.php';
require_once dirname(__FILE__) . '/../../../../../../../sys/classes/org/tubepress/impl/embedded/strategies/VimeoEmbeddedStrategy.class.php';

class org_tubepress_impl_embedded_VimeoEmbeddedStrategyTest extends org_tubepress_impl_embedded_AbstractEmbeddedStrategyTest {
    
    function testCanHandleVimeo()
    {
        $this->getSut()->start();
        $canHandle = $this->getSut()->canHandle(org_tubepress_api_provider_Provider::VIMEO, 'videoid');
        
        $this->assertTrue($canHandle);
    }
    
    function buildSut()
    {
        return new org_tubepress_impl_embedded_strategies_VimeoEmbeddedStrategy();
    }
    
    function expected()
    {
        return <<<EOT
<iframe src="http://player.vimeo.com/video/videoid?autoplay=0&amp;color=999999&amp;loop=0&amp;title=0&amp;byline=0&amp;portrait=0" width="425" height="350" frameborder="0"></iframe>

EOT;
    }

}

