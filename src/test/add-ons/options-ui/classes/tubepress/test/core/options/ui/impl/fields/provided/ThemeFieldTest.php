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
 * @covers tubepress_core_options_ui_impl_fields_provided_ThemeField<extended>
 */
class tubepress_test_core_options_ui_impl_fields_ThemeFieldTest extends tubepress_test_core_options_ui_impl_fields_provided_DropdownFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeRegistry;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeLibrary;

    public function testGetThemeDataAsJson()
    {
        $mockTheme  = $this->mock(tubepress_core_theme_api_ThemeInterface::_);
        $mockThemes = array($mockTheme);

        $mockTheme->shouldReceive('getName')->twice()->andReturn('theme name');
        $mockTheme->shouldReceive('getDescription')->once()->andReturn('theme description');
        $mockTheme->shouldReceive('getAuthor')->once()->andReturn('theme author');
        $mockTheme->shouldReceive('getLicenses')->once()->andReturn(array(
            array('type' => 'foo license', 'url' => 'http://foo.bar')
        ));
        $mockTheme->shouldReceive('getVersion')->once()->andReturn('8.0.0');
        $mockTheme->shouldReceive('getDemoUrl')->once()->andReturn('http://foo.bar/demo');
        $mockTheme->shouldReceive('getHomepageUrl')->once()->andReturn('http://foo.bar/home');
        $mockTheme->shouldReceive('getDocumentationUrl')->once()->andReturn('http://foo.bar/docs');
        $mockTheme->shouldReceive('getDownloadUrl')->once()->andReturn('http://foo.bar/download');
        $mockTheme->shouldReceive('getBugTrackerUrl')->once()->andReturn('http://foo.bar/bugs');
        $mockTheme->shouldReceive('getKeywords')->once()->andReturn(array('some', 'key', 'word'));
        $this->_mockThemeLibrary->shouldReceive('getScreenshots')->once()->with('theme name')->andReturn(array('some', 'screen', 'shot'));
        $this->_mockThemeRegistry->shouldReceive('getAll')->once()->andReturn($mockThemes);

        $actual = $this->getSut()->getThemeDataAsJson();
        $expected = '{"theme name":{"screenshots":["some","screen","shot"],"description":"theme description","author":"theme author","licenses":[{"type":"foo license","url":"http:\/\/foo.bar"}],"version":"8.0.0","demo":"http:\/\/foo.bar\/demo","keywords":["some","key","word"],"homepage":"http:\/\/foo.bar\/home","docs":"http:\/\/foo.bar\/docs","download":"http:\/\/foo.bar\/download","bugs":"http:\/\/foo.bar\/bugs"}}';

        $this->assertEquals($expected, $actual);
    }

    protected function buildSut()
    {
        return new tubepress_core_options_ui_impl_fields_provided_ThemeField(

            $this->getMockTranslator(),
            $this->getMockPersistence(),
            $this->getMockHttpRequestParams(),
            $this->getMockEventDispatcher(),
            $this->getMockOptionProvider(),
            $this->getMockTemplateFactory(),
            $this->getMockLangUtils(),
            $this->_mockThemeRegistry,
            $this->_mockThemeLibrary
        );
    }

    protected function onAfterDropDownFieldSetup()
    {
        $this->_mockThemeLibrary   = $this->mock(tubepress_core_theme_api_ThemeLibraryInterface::_);
        $this->_mockThemeRegistry  = $this->mock(tubepress_api_contrib_RegistryInterface::_);
    }

    protected function getOptionsPageItemId()
    {
        return 'theme';
    }
}
