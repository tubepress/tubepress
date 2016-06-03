<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com).
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_meta_impl_listeners_MetaDisplayListener
 */
class tubepress_test_meta_impl_listeners_MetaDisplayListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var Mockery\MockInterface
     */
    private $_mockIncomingEvent;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOptionsReference;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockMediaProvider;

    /**
     * @var tubepress_meta_impl_listeners_MetaDisplayListener
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockIncomingEvent    = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockOptionsReference = $this->mock(tubepress_api_options_ReferenceInterface::_);
        $this->_mockContext          = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockMediaProvider    = $this->mock(tubepress_spi_media_MediaProviderInterface::__);

        $this->_sut = new tubepress_meta_impl_listeners_MetaDisplayListener(
            $this->_mockContext,
            $this->_mockOptionsReference
        );

        $this->_sut->setMediaProviders(array($this->_mockMediaProvider));
    }

    public function testPreTemplate()
    {
        $this->_mockMediaProvider->shouldReceive('getMapOfMetaOptionNamesToAttributeDisplayNames')->once()->andReturn(array(
            'meta1' => 'attribute1',
            'meta2' => 'attribute2',
        ));

        $this->_mockIncomingEvent->shouldReceive('getSubject')->andReturnNull();

        $this->_mockOptionsReference->shouldReceive('getUntranslatedLabel')->once()
            ->with('meta1')->andReturn('meta1 label');
        $this->_mockOptionsReference->shouldReceive('getUntranslatedLabel')->once()
            ->with('meta2')->andReturn('meta2 label');

        $this->_mockContext->shouldReceive('get')->once()->with('meta1')->andReturn(false);
        $this->_mockContext->shouldReceive('get')->once()->with('meta2')->andReturn(true);

        $this->_mockIncomingEvent->shouldReceive('setSubject')->once()->with(array(
            tubepress_api_template_VariableNames::MEDIA_ITEM_ATTRIBUTE_LABELS => array(
                'attribute1' => 'meta1 label',
                'attribute2' => 'meta2 label',
            ),
            tubepress_api_template_VariableNames::MEDIA_ITEM_ATTRIBUTES_TO_SHOW => array(
                'attribute2',
            ),
        ));

        $this->_sut->onPreTemplate($this->_mockIncomingEvent);
        $this->assertTrue(true);
    }
}
