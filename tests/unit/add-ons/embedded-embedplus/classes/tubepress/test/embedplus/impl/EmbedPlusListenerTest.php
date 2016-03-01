<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_embedplus_impl_EmbedPlus
 */
class tubepress_test_embedplus_impl_EmbedPlusTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_embedplus_impl_EmbedPlus
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockUrlFactory;

    public function onSetup() {

        $this->_mockUrlFactory = $this->mock(tubepress_api_url_UrlFactoryInterface::_);

        $this->_sut = new tubepress_embedplus_impl_EmbedPlus($this->_mockUrlFactory);
    }

    public function testGalleryInit()
    {
        $mockEvent = $this->mock('tubepress_api_event_EventInterface');

        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array(
            'options' => array(
                tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL => 'embedplus',
                tubepress_api_options_Names::EMBEDDED_HEIGHT      => 44,
            )
        ));

        $mockEvent->shouldReceive('setSubject')->once()->with(
            array(
                'options' => array(
                    tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL => 'embedplus',
                    tubepress_api_options_Names::EMBEDDED_HEIGHT      => 74,
                )
            )
        );

        $this->_sut->onGalleryInitJs($mockEvent);
        $this->assertTrue(true);
    }

    public function testGalleryInitWrongImpl()
    {
        $mockEvent = $this->mock('tubepress_api_event_EventInterface');

        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array(
            'options' => array(
                tubepress_api_options_Names::EMBEDDED_PLAYER_IMPL => 'xyz'
            )
        ));

        $this->_sut->onGalleryInitJs($mockEvent);
        $this->assertTrue(true);
    }

    public function testGalleryInitJsNotSet()
    {
        $mockEvent = $this->mock('tubepress_api_event_EventInterface');

        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array());

        $this->_sut->onGalleryInitJs($mockEvent);
        $this->assertTrue(true);
    }

    public function testGetVariables()
    {
        $mockMediaItem = $this->mock('tubepress_api_media_MediaItem');
        $mockUrl       = $this->mock(tubepress_api_url_UrlInterface::_);

        $mockMediaItem->shouldReceive('getId')->once()->andReturn('abc');

        $this->_mockUrlFactory->shouldReceive('fromString')->once()->with(
            'http://www.youtube.com/embed/abc'
        )->andReturn($mockUrl);

        $actual   = $this->_sut->getTemplateVariables($mockMediaItem);
        $expected = array(
            tubepress_api_template_VariableNames::EMBEDDED_DATA_URL => $mockUrl
        );

        $this->assertEquals($expected, $actual);
    }

    public function testBasics()
    {
        $this->assertEquals('embedplus', $this->_sut->getName());
        $this->assertEquals('EmbedPlus', $this->_sut->getUntranslatedDisplayName());
        $this->assertEquals(array('youtube'), $this->_sut->getCompatibleMediaProviderNames());
        $this->assertEquals('single/embedded/embedplus', $this->_sut->getTemplateName());
        $this->assertEquals(array(TUBEPRESS_ROOT . '/src/add-ons/embedded-embedplus/templates'), $this->_sut->getTemplateDirectories());
    }
}

