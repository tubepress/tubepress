<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_vimeo_impl_embedded_VimeoEmbeddedPlayerTest extends tubepress_test_TubePressUnitTest
{
    private $_sut;

    public function onSetup() {

        $this->_sut = new tubepress_addons_vimeo_impl_embedded_VimeoPluggableEmbeddedPlayerService();
    }

    public function testGetName()
    {
        $this->assertEquals('vimeo', $this->_sut->getName());
    }

    public function testGetProviderName()
    {
        $this->assertEquals('vimeo', $this->_sut->getHandledProviderName());
    }

    public function testGetTemplate()
    {
        $mockThemeHandler = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandler::_);

        $mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with(

            'embedded/vimeo.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/addons/vimeo/resources/templates'
        )->andReturn('abc');

        $result = $this->_sut->getTemplate($mockThemeHandler);

        $this->assertEquals('abc', $result);
    }

    public function testGetDataUrl()
    {
        $mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::AUTOPLAY)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::LOOP)->andReturn(false);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::SHOW_INFO)->andReturn(true);
        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);

        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_addons_vimeo_api_const_options_names_Embedded::PLAYER_COLOR)->andReturn('ABCDEF');

        $result = $this->_sut->getDataUrlForVideo('xx');

        $this->assertTrue($result instanceof ehough_curly_Url);
        $this->assertRegExp('~^http://player\.vimeo\.com/video/xx\?autoplay=1&color=ABCDEF&loop=0&portrait=1&byline=1&title=1&api=1&player_id=tubepress-video-object-[0-9]+$~', $result->toString());
    }

}

