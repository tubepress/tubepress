<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_test_addons_embedplus_impl_embedded_EmbedPlusEmbeddedPlayerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_embedplus_impl_embedded_EmbedPlusPluggableEmbeddedPlayerService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup() {

        $this->_mockUrlFactory = $this->createMockSingletonService(tubepress_api_url_UrlFactoryInterface::_);
        $this->_sut = new tubepress_addons_embedplus_impl_embedded_EmbedPlusPluggableEmbeddedPlayerService($this->_mockUrlFactory);
    }

    public function testGetName()
    {
        $this->assertEquals('embedplus', $this->_sut->getName());
    }

    public function testGetProviderName()
    {
        $this->assertEquals('youtube', $this->_sut->getHandledProviderName());
    }

    public function testGetTemplate()
    {
        $mockThemeHandler = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandlerInterface::_);

        $mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with(

            'embedded/embedplus.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/add-ons/embedplus/resources/templates'
        )->andReturn('abc');

        $result = $this->_sut->getTemplate($mockThemeHandler);

        $this->assertEquals('abc', $result);
    }

    public function testGetDataUrl()
    {
        $mockUrl = ehough_mockery_Mockery::mock('tubepress_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://www.youtube.com/embed/xx')->andReturn($mockUrl);

        $result = $this->_sut->getDataUrlForVideo('xx');

        $this->assertSame($mockUrl, $result);
    }

}

