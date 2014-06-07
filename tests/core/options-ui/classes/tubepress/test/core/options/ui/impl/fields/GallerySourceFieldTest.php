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
 * @covers tubepress_core_options_ui_impl_fields_GallerySourceField<extended>
 */
class tubepress_test_core_options_ui_impl_fields_GallerySourceFieldTest extends tubepress_test_core_options_ui_impl_fields_AbstractOptionsPageFieldTest
{
    public function testIsPro()
    {
        $this->assertFalse($this->getSut()->isProOnly());
    }

    public function testGetWidgetHtml()
    {
        $this->assertEquals('', $this->getSut()->getWidgetHTML());
    }

    public function testOnSubmitWithError()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE)->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE)->andReturn('a');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE, 'a')->andReturn('some problem');

        $result = $this->getSut()->onSubmit();

        $this->assertEquals('some problem', $result);
    }

    public function testOnSubmitNoError()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE)->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE)->andReturn('a');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE, 'a')->andReturn(null);

        $result = $this->getSut()->onSubmit();

        $this->assertNull($result);
    }

    public function testOnSubmitMissing()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE)->andReturn(false);

        $result = $this->getSut()->onSubmit();

        $this->assertNull($result);
    }

    public function testGetDescription()
    {
        $result = $this->getSut()->getTranslatedDescription();

        $this->assertEquals('', $result);
    }

    /**
     * @return tubepress_core_options_ui_impl_fields_AbstractOptionsPageField
     */
    protected function buildSut()
    {
        return new tubepress_core_options_ui_impl_fields_GallerySourceField(

            $this->getMockTranslator(),
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams()
        );
    }

    /**
     * @return tubepress_core_options_ui_impl_fields_GallerySourceField
     */
    protected function getSut()
    {
        return parent::getSut();
    }

    /**
     * @return string
     */
    protected function getOptionsPageItemId()
    {
        return tubepress_core_media_provider_api_Constants::OPTION_GALLERY_SOURCE;
    }
}
