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
 * @covers tubepress_app_impl_options_ui_fields_GallerySourceField<extended>
 */
class tubepress_test_app_impl_options_ui_fields_GallerySourceFieldTest extends tubepress_test_app_impl_options_ui_fields_AbstractFieldTest
{
    /**
     * @var tubepress_app_impl_options_ui_fields_GallerySourceField
     */
    private $_sut;
    
    public function onAfterAbstractFieldSetup()
    {
        $this->_sut = new tubepress_app_impl_options_ui_fields_GallerySourceField(
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams()
        );
    }
    
    public function testIsPro()
    {
        $this->assertFalse($this->_sut->isProOnly());
    }

    public function testGetWidgetHtml()
    {
        $this->assertEquals('', $this->_sut->getWidgetHTML());
    }

    public function testOnSubmitWithError()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with(tubepress_app_api_options_Names::GALLERY_SOURCE)->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with(tubepress_app_api_options_Names::GALLERY_SOURCE)->andReturn('a');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_app_api_options_Names::GALLERY_SOURCE, 'a')->andReturn('some problem');

        $result = $this->_sut->onSubmit();

        $this->assertEquals('some problem', $result);
    }

    public function testOnSubmitNoError()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with(tubepress_app_api_options_Names::GALLERY_SOURCE)->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with(tubepress_app_api_options_Names::GALLERY_SOURCE)->andReturn('a');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_app_api_options_Names::GALLERY_SOURCE, 'a')->andReturn(null);

        $result = $this->_sut->onSubmit();

        $this->assertNull($result);
    }

    public function testOnSubmitMissing()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with(tubepress_app_api_options_Names::GALLERY_SOURCE)->andReturn(false);

        $result = $this->_sut->onSubmit();

        $this->assertNull($result);
    }

    public function testGetDescription()
    {
        $result = $this->_sut->getUntranslatedDescription();

        $this->assertEquals('', $result);
    }

    /**
     * @return tubepress_app_impl_options_ui_fields_AbstractField
     */
    protected function buildSut()
    {
        return new tubepress_app_impl_options_ui_fields_GallerySourceField(

            $this->getMockPersistence(),
            $this->getMockHttpRequestParams()
        );
    }

    /**
     * @return string
     */
    protected function getOptionsPageItemId()
    {
        return tubepress_app_api_options_Names::GALLERY_SOURCE;
    }
}
