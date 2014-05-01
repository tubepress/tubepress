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
 * @covers tubepress_addons_core_impl_options_ui_fields_ThemeField<extended>
 */
class tubepress_test_addons_core_impl_options_ui_fields_ThemeFieldTest extends tubepress_test_impl_options_ui_fields_DropdownFieldTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_themeFinder;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_themeHandler;

    public function testGetThemeDataAsJson()
    {
        $mockTheme  = ehough_mockery_Mockery::mock(tubepress_spi_theme_ThemeInterface::_);
        $mockThemes = array($mockTheme);

        $mockTheme->shouldReceive('getName')->twice()->andReturn('theme name');
        $mockTheme->shouldReceive('getDescription')->once()->andReturn('theme description');
        $mockTheme->shouldReceive('getAuthor')->once()->andReturn('theme author');
        $mockTheme->shouldReceive('getLicenses')->once()->andReturn(array(
            array('type' => 'foo license', 'url' => 'http://foo.bar')
        ));
        $mockTheme->shouldReceive('getVersion')->once()->andReturn(new tubepress_api_version_Version(8));
        $mockTheme->shouldReceive('getDemoUrl')->once()->andReturn('http://foo.bar/demo');
        $mockTheme->shouldReceive('getHomepageUrl')->once()->andReturn('http://foo.bar/home');
        $mockTheme->shouldReceive('getDocumentationUrl')->once()->andReturn('http://foo.bar/docs');
        $mockTheme->shouldReceive('getDownloadUrl')->once()->andReturn('http://foo.bar/download');
        $mockTheme->shouldReceive('getBugTrackerUrl')->once()->andReturn('http://foo.bar/bugs');
        $mockTheme->shouldReceive('getKeywords')->once()->andReturn(array('some', 'key', 'word'));
        $this->_themeHandler->shouldReceive('getScreenshots')->once()->with('theme name')->andReturn(array('some', 'screen', 'shot'));
        $this->_themeFinder->shouldReceive('findAllThemes')->once()->andReturn($mockThemes);

        $actual = $this->getSut()->getThemeDataAsJson();
        $expected = '{"theme name":{"screenshots":["some","screen","shot"],"description":"theme description","author":"theme author","licenses":[{"type":"foo license","url":"http:\/\/foo.bar"}],"version":"8.0.0","demo":"http:\/\/foo.bar\/demo","keywords":["some","key","word"],"homepage":"http:\/\/foo.bar\/home","docs":"http:\/\/foo.bar\/docs","download":"http:\/\/foo.bar\/download","bugs":"http:\/\/foo.bar\/bugs"}}';

        $this->assertEquals($expected, $actual);
    }

    protected function buildSut()
    {
        return new tubepress_addons_core_impl_options_ui_fields_ThemeField(
            $this->getMockStorageManager(),
            $this->getMockMessageService()
        );
    }

    /**
     * @return string
     */
    protected function getOptionName()
    {
        return tubepress_api_const_options_names_Thumbs::THEME;
    }

    protected function performAdditionalSetup()
    {
        $this->_themeHandler = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandlerInterface::_);
        $this->_themeFinder  = $this->createMockSingletonService(tubepress_spi_theme_ThemeFinderInterface::_);
    }

    protected function getExpectedFieldId()
    {
        return 'theme';
    }

    protected function getExpectedUntranslatedFieldLabel()
    {
        return 'the label';
    }

    protected function getExpectedUntranslatedFieldDescription()
    {
        return 'the description';
    }
}
