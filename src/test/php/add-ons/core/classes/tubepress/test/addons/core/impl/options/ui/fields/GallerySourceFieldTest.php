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
 * @covers tubepress_addons_core_impl_options_ui_fields_GallerySourceField<extended>
 */
class tubepress_test_addons_core_impl_options_ui_fields_GallerySourceFieldTest extends tubepress_test_impl_options_ui_fields_AbstractOptionsPageFieldTest
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
        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn(true);
        $this->getMockHttpRequestParameterService()->shouldReceive('getParamValue')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('a');

        $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE, 'a')->andReturn('some problem');

        $result = $this->getSut()->onSubmit();

        $this->assertEquals('some problem', $result);
    }

    public function testOnSubmitNoError()
    {
        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn(true);
        $this->getMockHttpRequestParameterService()->shouldReceive('getParamValue')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn('a');

        $this->getMockStorageManager()->shouldReceive('queueForSave')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE, 'a')->andReturn(null);

        $result = $this->getSut()->onSubmit();

        $this->assertNull($result);
    }

    public function testOnSubmitMissing()
    {
        $this->getMockHttpRequestParameterService()->shouldReceive('hasParam')->once()->with(tubepress_api_const_options_names_Output::GALLERY_SOURCE)->andReturn(false);

        $result = $this->getSut()->onSubmit();

        $this->assertNull($result);
    }

    /**
     * @return tubepress_impl_options_ui_fields_AbstractOptionsPageField
     */
    protected function buildSut()
    {
        return new tubepress_addons_core_impl_options_ui_fields_GallerySourceField(
            $this->getMockMessageService(),
            $this->getMockStorageManager()
        );
    }

    /**
     * @return string
     */
    protected function getExpectedTemplatePath()
    {
        return '';
    }

    /**
     * @return void
     */
    protected function prepareForGetWidgetHtml(ehough_mockery_mockery_MockInterface $template)
    {
        //do nothing
    }

    protected function getExpectedFieldId()
    {
        return tubepress_api_const_options_names_Output::GALLERY_SOURCE;
    }

    protected function getExpectedUntranslatedFieldLabel()
    {
        return null;
    }

    protected function getExpectedUntranslatedFieldDescription()
    {
        return null;
    }
}
