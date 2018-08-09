<?php
/*
 * Copyright 2006 - 2018 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_deprecated_impl_listeners_LegacyMetadataTemplateListener
 */
class tubepress_deprecated_impl_listeners_LegacyMetadataTemplateListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_deprecated_impl_listeners_LegacyMetadataTemplateListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockOptionReference;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTranslator;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockMediaProvider;

    public function onSetup()
    {

        $this->_mockExecutionContext = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockOptionReference  = $this->mock(tubepress_api_options_ReferenceInterface::_);
        $this->_mockMediaProvider    = $this->mock(tubepress_spi_media_MediaProviderInterface::_);
        $this->_mockTranslator       = $this->mock(tubepress_api_translation_TranslatorInterface::_);

        $this->_sut = new tubepress_deprecated_impl_listeners_LegacyMetadataTemplateListener(
            $this->_mockExecutionContext,
            $this->_mockOptionReference,
            $this->_mockTranslator
        );

        $this->_sut->setMediaProviders(array($this->_mockMediaProvider));
    }

    public function testOnTemplate()
    {
        $video = new tubepress_api_media_MediaItem('video-id');

        $mockTemplate = array('foo' => 'bar');

        $event = $this->mock('tubepress_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $event->shouldReceive('setSubject')->once()->with(array(
            'foo'                                                   => 'bar',
            tubepress_api_const_template_Variable::META_SHOULD_SHOW => array('meta' => '<<value of meta>>'),
            tubepress_api_const_template_Variable::META_LABELS      => array('meta' => '##video-meta##'),
        ));

        $this->_testVideoMetaStuff($mockTemplate);

        $this->_sut->onTemplate($event);

        $this->assertTrue(true);
    }

    private function _testVideoMetaStuff(array $mockTemplate)
    {
        $this->_mockMediaProvider->shouldReceive('getMapOfMetaOptionNamesToAttributeDisplayNames')->once()->andReturn(array(

            'meta' => 'something',
        ));

        $this->_mockExecutionContext->shouldReceive('get')->once()->with('meta')->andReturn('<<value of meta>>');
        $this->_mockOptionReference->shouldReceive('optionExists')->once()->with('meta')->andReturn(true);
        $this->_mockOptionReference->shouldReceive('getUntranslatedLabel')->once()->with('meta')->andReturn('meta label!');
        $this->_mockTranslator->shouldReceive('trans')->once()->with('meta label!')->andReturn('##video-meta##');
    }
}
