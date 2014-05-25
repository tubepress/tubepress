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
 * @covers tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider
 */
class tubepress_test_jwplayer_impl_embedded_JwPlayerEmbeddedProviderTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider
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


        $this->_mockUrlFactory = $this->mock(tubepress_core_api_url_UrlFactoryInterface::_);
        $this->_mockTemplateFactory = $this->mock(tubepress_core_api_template_TemplateFactoryInterface::_);
        $this->_sut = new tubepress_jwplayer_impl_embedded_JwPlayerEmbeddedProvider($this->_mockUrlFactory, $this->_mockTemplateFactory);
    }

    public function testCanHandleYes()
    {
        $this->assertEquals(array('youtube'), $this->_sut->getCompatibleProviderNames());
    }

    public function testGetName()
    {
        $this->assertEquals('longtail', $this->_sut->getName());
    }

    public function testGetFriendlyName()
    {
        $this->assertEquals('JW Player (by Longtail Video)', $this->_sut->getUntranslatedDisplayName());
    }

    public function testGetTemplate()
    {
        $expected = array(

            'embedded/longtail.tpl.php',
            TUBEPRESS_ROOT . '/src/main/php/add-ons/jwplayer/resources/templates'
        );

        $result = $this->_sut->getPathsForTemplateFactory();

        $this->assertEquals($expected, $result);
    }

    public function testGetDataUrl()
    {
        $mockUrl = $this->mock('tubepress_core_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://www.youtube.com/watch?v=xx')->andReturn($mockUrl);

        $mockProvider = $this->mock(tubepress_core_api_provider_VideoProviderInterface::_);

        $result = $this->_sut->getDataUrlForVideo($this->_mockUrlFactory, $mockProvider, 'xx');

        $this->assertSame($mockUrl, $result);
    }
}