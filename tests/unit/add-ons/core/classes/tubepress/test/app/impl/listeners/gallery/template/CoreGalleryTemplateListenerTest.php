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
 * @covers tubepress_app_impl_listeners_gallery_template_CoreGalleryTemplateListener
 */
class tubepress_test_app_impl_listeners_gallery_template_CoreGalleryTemplateListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_gallery_template_CoreGalleryTemplateListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockTranslator       = $this->mock(tubepress_lib_api_translation_TranslatorInterface::_);
        $this->_mockExecutionContext = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockEvent            = $this->mock('tubepress_lib_api_event_EventInterface');

        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn(array('foo' => 'bar'));

        $this->_sut = new tubepress_app_impl_listeners_gallery_template_CoreGalleryTemplateListener(
            $this->_mockExecutionContext,
            $this->_mockTranslator
        );
    }

    public function testOnTemplate()
    {
        $providerResult = new tubepress_app_api_media_MediaPage();
        $providerResult->setItems(array('video-array'));

        $expected = array(
            tubepress_app_api_template_VariableNames::MEDIA_PAGE                  => $providerResult,
            tubepress_app_api_template_VariableNames::HTML_WIDGET_ID              => 47,
            tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_WIDTH_PX  => 556,
            tubepress_app_api_template_VariableNames::GALLERY_THUMBNAIL_HEIGHT_PX => 984,
        );

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('page')->andReturn($providerResult);
        $this->_mockEvent->shouldReceive('setSubject')->once()->with(array_merge(array('foo' => 'bar'), $expected));

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::HTML_GALLERY_ID)->andReturn(47);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_THUMB_WIDTH)->andReturn(556);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_THUMB_HEIGHT)->andReturn(984);

        $this->_sut->onGalleryTemplatePreRender($this->_mockEvent);

        $this->assertTrue(true);
    }
}