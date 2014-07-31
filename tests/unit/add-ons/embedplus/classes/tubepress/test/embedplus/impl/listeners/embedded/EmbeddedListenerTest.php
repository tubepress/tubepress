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
 * @covers tubepress_embedplus_impl_listeners_embedded_EmbeddedListener
 */
class tubepress_test_embedplus_impl_listeners_embedded_EmbeddedListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_embedplus_impl_listeners_embedded_EmbeddedListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    public function onSetup() {

        $this->_mockUrlFactory = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockTemplating = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);

        $this->_sut = new tubepress_embedplus_impl_listeners_embedded_EmbeddedListener($this->_mockUrlFactory, $this->_mockTemplating);
    }

    public function testGetName()
    {
        $this->assertEquals('embedplus', $this->_sut->getName());
    }

    public function testGetTemplate()
    {
        $expected = array(
                'embedded/embedplus.tpl.php',
                TUBEPRESS_ROOT . '/src/add-ons/embedplus/templates/embedplus.tpl.php'
            );

        $result = $this->_sut->getPathsForTemplateFactory();

        $this->assertEquals($expected, $result);
    }

    public function testGetDataUrl()
    {
        $mockUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with('http://www.youtube.com/embed/xx')->andReturn($mockUrl);


        $result = $this->_sut->getDataUrlForMediaItem($this->_mockUrlFactory, 'xx');

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

