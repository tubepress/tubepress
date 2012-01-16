<?php

require_once BASE . '/sys/classes/org/tubepress/impl/embedded/commands/YouTubeIframeCommand.class.php';

class org_tubepress_impl_embedded_commands_YouTubeIframeCommandTest extends TubePressUnitTest {

    private $_sut;

    public function setUp() {

        parent::setUp();
        $this->_sut = new org_tubepress_impl_embedded_commands_YouTubeIframeCommand();
    }

    function testCannotHandleVimeo()
    {
        $mockChainContext               = \Mockery::mock('stdClass');
        $mockChainContext->providerName = org_tubepress_api_provider_Provider::VIMEO;
        $mockChainContext->videoId      = 'video_id';

        $this->assertFalse($this->_sut->execute($mockChainContext));
    }

    function testCanHandleYouTube()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR)->andReturn('123456');
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_HIGHLIGHT)->andReturn('654321');
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::SHOW_RELATED)->andReturn(true);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::FULLSCREEN)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::HIGH_QUALITY)->andReturn(true);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Advanced::GALLERY_ID)->andReturn('some-gallery-id');

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');

        $mockChainContext               = \Mockery::mock('stdClass');
        $mockChainContext->providerName = org_tubepress_api_provider_Provider::YOUTUBE;
        $mockChainContext->videoId      = 'video_id';

        $theme = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $theme->shouldReceive('getTemplateInstance')->once()->with('embedded_flash/youtube.tpl.php')->andReturn($mockTemplate);

        $this->assertTrue($this->_sut->execute($mockChainContext));

        $this->assertEquals($mockTemplate, $mockChainContext->template);
        $this->assertEquals('http://www.youtube.com/embed/video_id?color2=0x123456&color1=0x654321&rel=1&autoplay=0&loop=0&fs=0&showinfo=1&wmode=transparent&enablejsapi=1&hd=1',
            $mockChainContext->dataUrl->toString());
        $this->assertEquals('youtube', $mockChainContext->embeddedImplementationName);
    }

}
