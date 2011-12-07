<?php

require_once BASE . '/sys/classes/org/tubepress/impl/embedded/commands/JwFlvCommand.class.php';

class org_tubepress_impl_embedded_commands_JwFlvCommandTest extends TubePressUnitTest {

    private $_sut;

    public function setUp() {

        parent::setUp();
        $this->_sut = new org_tubepress_impl_embedded_commands_JwFlvCommand();
    }

    function testCannotHandleYouTubeWithDefault()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();
        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn(org_tubepress_api_const_options_values_PlayerImplementationValue::PROVIDER_BASED);

        $mockChainContext = \Mockery::mock('stdClass');
        $mockChainContext->providerName = org_tubepress_api_provider_Provider::YOUTUBE;
        $mockChainContext->videoId = 'video_id';

        $this->assertFalse($this->_sut->execute($mockChainContext));
    }

    function testCannotHandleVimeoWithLongtail()
    {
        $mockChainContext = \Mockery::mock('stdClass');
        $mockChainContext->providerName = org_tubepress_api_provider_Provider::VIMEO;
        $mockChainContext->videoId = 'video_id';

        $this->assertFalse($this->_sut->execute($mockChainContext));
    }

    function testCanHandleYouTubeWithLongtail()
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');

        $execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Embedded::PLAYER_IMPL)->andReturn(org_tubepress_api_const_options_values_PlayerImplementationValue::LONGTAIL);

        $mockChainContext = \Mockery::mock('stdClass');
        $mockChainContext->providerName = org_tubepress_api_provider_Provider::YOUTUBE;
        $mockChainContext->videoId = 'video_id';

        $theme = $ioc->get(org_tubepress_api_theme_ThemeHandler::_);
        $theme->shouldReceive('getTemplateInstance')->once()->with('embedded_flash/longtail.tpl.php')->andReturn($mockTemplate);

        $this->assertTrue($this->_sut->execute($mockChainContext));
        $this->assertEquals('http://www.youtube.com/watch?v=video_id', $mockChainContext->dataUrl->toString());
        $this->assertEquals('longtail', $mockChainContext->embeddedImplementationName);
        $this->assertEquals($mockTemplate, $mockChainContext->template);
    }
}

