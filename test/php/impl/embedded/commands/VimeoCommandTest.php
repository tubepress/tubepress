<?php

require_once BASE . '/sys/classes/org/tubepress/impl/embedded/commands/VimeoCommand.class.php';

class org_tubepress_impl_embedded_commands_VimeoCommandTest extends TubePressUnitTest {

    private $_sut;

    public function setUp() {

        parent::setUp();
        $this->_sut = new org_tubepress_impl_embedded_commands_VimeoCommand();
    }

    function testCannotHandleYouTube()
    {
        $mockChainContext               = \Mockery::mock('stdClass');
        $mockChainContext->providerName = org_tubepress_api_provider_Provider::YOUTUBE;
        $mockChainContext->videoId      = 'video_id';

        $this->assertFalse($this->_sut->execute($mockChainContext));
    }

    function testCanHandleVimeo()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(false);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_COLOR)->andReturn('123456');
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');

        $mockChainContext               = \Mockery::mock('stdClass');
        $mockChainContext->providerName = org_tubepress_api_provider_Provider::VIMEO;
        $mockChainContext->videoId      = 'video_id';

        $theme = $ioc->get('org_tubepress_api_theme_ThemeHandler');
        $theme->shouldReceive('getTemplateInstance')->once()->with('embedded_flash/vimeo.tpl.php')->andReturn($mockTemplate);

        $this->assertTrue($this->_sut->execute($mockChainContext));

        $this->assertEquals($mockTemplate, $mockChainContext->template);
        $this->assertEquals('http://player.vimeo.com/video/video_id?autoplay=0&color=123456&loop=0&title=1&byline=1&portrait=1', $mockChainContext->dataUrl->toString());
        $this->assertEquals('vimeo', $mockChainContext->embeddedImplementationName);
    }

}

