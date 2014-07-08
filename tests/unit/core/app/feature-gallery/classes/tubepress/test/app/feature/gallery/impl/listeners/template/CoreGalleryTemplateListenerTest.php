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
 * @covers tubepress_app_feature_gallery_impl_listeners_template_CoreGalleryTemplateListener
 */
class tubepress_test_app_feature_gallery_impl_listeners_template_CoreGalleryTemplateListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_feature_gallery_impl_listeners_template_CoreGalleryTemplateListener
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

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplate;

    public function onSetup()
    {
        $this->_mockTranslator = $this->mock(tubepress_lib_translation_api_TranslatorInterface::_);
        $this->_mockExecutionContext = $this->mock(tubepress_app_options_api_ContextInterface::_);
        $this->_mockEvent = $this->mock('tubepress_lib_event_api_EventInterface');
        $this->_mockTemplate = $this->mock('tubepress_lib_template_api_TemplateInterface');
        $this->_mockEvent->shouldReceive('getSubject')->once()->andReturn($this->_mockTemplate);

        $this->_sut = new tubepress_app_feature_gallery_impl_listeners_template_CoreGalleryTemplateListener(
            $this->_mockExecutionContext,
            $this->_mockTranslator
        );
    }

    public function testOnTemplate()
    {
        $this->_testGalleryIdAndItems();
        $this->_testThumbSizes();
        $this->_testTranslator();

        $this->_sut->onGalleryTemplate($this->_mockEvent);

        $this->assertTrue(true);
    }

    private function _testTranslator()
    {
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with('translator', $this->_mockTranslator);
    }

    private function _testGalleryIdAndItems()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_html_api_Constants::OPTION_GALLERY_ID)->andReturn(47);

        $providerResult = new tubepress_app_media_provider_api_Page();
        $providerResult->setItems(array('video-array'));

        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_MEDIA_ITEM_ARRAY, array('video-array'));
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_html_api_Constants::TEMPLATE_VAR_GALLERY_ID, 47);

        $this->_mockEvent->shouldReceive('getArgument')->once()->with('page')->andReturn($providerResult);
    }

    private function _testThumbSizes()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_WIDTH)->andReturn(556);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_app_feature_gallery_api_Constants::OPTION_THUMB_HEIGHT)->andReturn(984);

        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_WIDTH, 556);
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_app_feature_gallery_api_Constants::TEMPLATE_VAR_THUMBNAIL_HEIGHT, 984);
    }
}