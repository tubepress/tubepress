<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_options_ui_impl_fields_GallerySourceField<extended>
 */
class tubepress_test_app_impl_options_ui_fields_GallerySourceFieldTest extends tubepress_test_app_impl_options_ui_fields_AbstractFieldTest
{
    /**
     * @var tubepress_options_ui_impl_fields_GallerySourceField
     */
    private $_sut;

    private static $_PARAM_NAME;

    public function onAfterAbstractFieldSetup()
    {
        self::$_PARAM_NAME = tubepress_api_options_Names::GALLERY_SOURCE;

        $this->_sut = new tubepress_options_ui_impl_fields_GallerySourceField(
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams()
        );
    }

    public function testCloneForMultiSource()
    {
        $mockPersistence = $this->mock(tubepress_api_options_PersistenceInterface::_);

        $actual = $this->_sut->cloneForMultiSource('xyz', $mockPersistence);

        $this->assertInstanceOf('tubepress_options_ui_impl_fields_GallerySourceField', $actual);

        $this->assertNotSame($this->_sut, $actual);
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
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with(self::$_PARAM_NAME)->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with(self::$_PARAM_NAME)->andReturn('a');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_api_options_Names::GALLERY_SOURCE, 'a')->andReturn('some problem');

        $result = $this->_sut->onSubmit();

        $this->assertEquals('some problem', $result);
    }

    public function testOnSubmitNoError()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with(self::$_PARAM_NAME)->andReturn(true);
        $this->getMockHttpRequestParams()->shouldReceive('getParamValue')->once()->with(self::$_PARAM_NAME)->andReturn('a');

        $this->getMockPersistence()->shouldReceive('queueForSave')->once()->with(tubepress_api_options_Names::GALLERY_SOURCE, 'a')->andReturn(null);

        $result = $this->_sut->onSubmit();

        $this->assertNull($result);
    }

    public function testOnSubmitMissing()
    {
        $this->getMockHttpRequestParams()->shouldReceive('hasParam')->once()->with(self::$_PARAM_NAME)->andReturn(false);

        $result = $this->_sut->onSubmit();

        $this->assertNull($result);
    }

    public function testGetDescription()
    {
        $result = $this->_sut->getUntranslatedDescription();

        $this->assertEquals('', $result);
    }
}
