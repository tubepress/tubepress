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

/**
 * @covers tubepress_embedplus_impl_embedded_EmbedPlusEmbeddedProviderService
 */
class tubepress_test_embedplus_impl_embedded_EmbedPlusEmbeddedProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_embedplus_impl_embedded_EmbedPlusEmbeddedProviderService
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplateFactory;

    public function onSetup() {

        $this->_mockUrlFactory = $this->mock(tubepress_core_url_api_UrlFactoryInterface::_);
        $this->_mockTemplateFactory = $this->mock(tubepress_core_template_api_TemplateFactoryInterface::_);

        $this->_sut = new tubepress_embedplus_impl_embedded_EmbedPlusEmbeddedProviderService($this->_mockUrlFactory, $this->_mockTemplateFactory);
    }

    public function testGetName()
    {
        $this->assertEquals('embedplus', $this->_sut->getName());
    }

    public function testGetTemplate()
    {
        $expected = array(
                'embedded/embedplus.tpl.php',
                TUBEPRESS_ROOT . '/src/main/add-ons/embedplus/resources/templates/embedded/embedplus.tpl.php'
            );

        $result = $this->_sut->getPathsForTemplateFactory();

        $this->assertEquals($expected, $result);
    }

    public function testGetDataUrl()
    {
        $mockUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://www.youtube.com/embed/xx')->andReturn($mockUrl);

        $mockProvider = $this->mock(tubepress_core_provider_api_MediaProviderInterface::_);

        $result = $this->_sut->getDataUrlForVideo($this->_mockUrlFactory, $mockProvider, 'xx');

        $this->assertSame($mockUrl, $result);
    }

    public function testGetFriendlyName()
    {
        $this->assertEquals('EmbedPlus', $this->_sut->getUntranslatedDisplayName());
    }

    public function testCanHandleVideosTrue()
    {
        $this->assertEquals(array('youtube'), $this->_sut->getCompatibleProviderNames());
    }
}

