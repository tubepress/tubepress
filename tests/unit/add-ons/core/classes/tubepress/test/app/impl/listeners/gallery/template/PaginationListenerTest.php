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
 * @covers tubepress_app_impl_listeners_gallery_template_PaginationTemplateListener
 */
class tubepress_test_app_impl_listeners_gallery_template_PaginationListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_gallery_template_PaginationTemplateListener
     */
    private $_sut;

    /**
     * @var tubepress_app_api_media_MediaPage
     */
    private $_providerResult;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockRequestParams;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeTemplateLocator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFullUrl;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFullQuery;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTranslator;

    public function onSetup()
    {
        $this->_mockContext                     = $this->mock(tubepress_app_api_options_ContextInterface::_);
        $this->_mockUrlFactory                  = $this->mock(tubepress_platform_api_url_UrlFactoryInterface::_);
        $this->_mockRequestParams = $this->mock(tubepress_lib_api_http_RequestParametersInterface::_);
        $this->_mockTemplating                  = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);
        $this->_mockThemeTemplateLocator        = $this->mock('tubepress_app_impl_template_ThemeTemplateLocator');
        $this->_mockTranslator                  = $this->mock(tubepress_lib_api_translation_TranslatorInterface::_);

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_PAGINATE_ABOVE)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::GALLERY_PAGINATE_BELOW)->andReturn(true);
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_app_api_options_Names::FEED_RESULTS_PER_PAGE)->andReturn(4);

        $this->_providerResult = new tubepress_app_api_media_MediaPage();
        $this->_providerResult->setTotalResultCount(500);

        $this->_mockFullUrl = $this->mock('tubepress_platform_api_url_UrlInterface');
        $this->_mockFullQuery = $this->mock('tubepress_platform_api_url_QueryInterface');
        $this->_mockUrlFactory->shouldReceive('fromCurrent')->once()->andReturn($this->_mockFullUrl);

        $this->_mockTranslator->shouldReceive('_')->atLeast(1)->andReturnUsing(function ($original) {
            return "##$original##";
        });

        $this->_sut = new tubepress_app_impl_listeners_gallery_template_PaginationTemplateListener(
            
            $this->_mockContext,
            $this->_mockUrlFactory,
            $this->_mockRequestParams,
            $this->_mockTemplating,
            $this->_mockThemeTemplateLocator,
            $this->_mockTranslator
        );
    }

    public function testModern()
    {
        $this->_mockThemeTemplateLocator->shouldReceive('getSource')->once()->with('pagination')->andReturn('good!');
        $newTemplateVars = array(

            tubepress_app_api_template_VariableNames::GALLERY_PAGINATION_CURRENT_PAGE_NUMBER => 25,
            tubepress_app_api_template_VariableNames::GALLERY_PAGINATION_TOTAL_ITEMS         => 500,
            tubepress_app_api_template_VariableNames::GALLERY_PAGINATION_HREF_FORMAT         => 'foobar',
            tubepress_app_api_template_VariableNames::GALLERY_PAGINATION_RESULTS_PER_PAGE    => 4,
        );
        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('pagination', $newTemplateVars)->andReturn('foo');

        $this->_mockRequestParams->shouldReceive('getParamValueAsInt')->once()->with('tubepress_page', 1)->andReturn(25);

        $this->_mockFullUrl->shouldReceive('removeSchemeAndAuthority')->once();
        $this->_mockFullUrl->shouldReceive('getQuery')->once()->andReturn($this->_mockFullQuery);
        $this->_mockFullUrl->shouldReceive('__toString')->once()->andReturn('foobar');

        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', '%d');

        $this->_test('foo');
    }

    public function testLegacyHighPage()
    {
        $this->_mockThemeTemplateLocator->shouldReceive('getSource')->once()->with('pagination')->andReturnNull();
        $expectedHtml = file_get_contents(TUBEPRESS_ROOT . '/tests/unit/add-ons/core/fixtures/feature/gallery/pagination/legacy-high.html');

        $this->_mockRequestParams->shouldReceive('getParamValueAsInt')->once()->with('tubepress_page', 1)->andReturn(25);

        $this->_mockFullQuery->shouldReceive('remove')->once()->with('tubepress_page');
        $this->_mockFullQuery->shouldReceive('set')->twice()->with('tubepress_page', 24);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 1);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 2);
        $this->_mockFullQuery->shouldReceive('set')->twice()->with('tubepress_page', 26);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 124);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 125);
        $this->_mockFullUrl->shouldReceive('getQuery')->times(9)->andReturn($this->_mockFullQuery);
        $this->_mockFullUrl->shouldReceive('__toString')->atLeast(1)->andReturn('/foo.bar?hello=goodbye&something=el%21se');

        $this->_test($expectedHtml);
    }

    public function testLegacyMiddlePage()
    {
        $this->_mockThemeTemplateLocator->shouldReceive('getSource')->once()->with('pagination')->andReturnNull();
        $expectedHtml = file_get_contents(TUBEPRESS_ROOT . '/tests/unit/add-ons/core/fixtures/feature/gallery/pagination/legacy-middle.html');

        $this->_mockRequestParams->shouldReceive('getParamValueAsInt')->once()->with('tubepress_page', 1)->andReturn(12);

        $this->_mockFullQuery->shouldReceive('remove')->once()->with('tubepress_page');
        $this->_mockFullQuery->shouldReceive('set')->twice()->with('tubepress_page', 11);
        $this->_mockFullQuery->shouldReceive('set')->twice()->with('tubepress_page', 13);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 1);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 2);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 124);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 125);
        $this->_mockFullUrl->shouldReceive('getQuery')->times(9)->andReturn($this->_mockFullQuery);
        $this->_mockFullUrl->shouldReceive('__toString')->atLeast(1)->andReturn('/foo.bar?hello=goodbye&something=el%21se');


        $this->_test($expectedHtml);
    }

    public function testLegacy()
    {
        $this->_mockThemeTemplateLocator->shouldReceive('getSource')->once()->with('pagination')->andReturnNull();
        $expectedHtml = file_get_contents(TUBEPRESS_ROOT . '/tests/unit/add-ons/core/fixtures/feature/gallery/pagination/legacy-low.html');

        $this->_mockRequestParams->shouldReceive('getParamValueAsInt')->once()->with('tubepress_page', 1)->andReturn(1);

        $this->_mockFullQuery->shouldReceive('remove')->once()->with('tubepress_page');
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 3);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 4);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 5);
        $this->_mockFullQuery->shouldReceive('set')->twice()->with('tubepress_page', 2);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 124);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 125);
        $this->_mockFullUrl->shouldReceive('getQuery')->times(8)->andReturn($this->_mockFullQuery);
        $this->_mockFullUrl->shouldReceive('__toString')->atLeast(1)->andReturn('/foo.bar?hello=goodbye&something=el%21se');


        $this->_test($expectedHtml);
    }

    private function _test($finalPaginationHtml)
    {
        $initial = array('abc' => 'xyz');
        $final   = array(
            tubepress_app_api_template_VariableNames::GALLERY_PAGINATION_HTML     => $finalPaginationHtml,
            tubepress_app_api_template_VariableNames::GALLERY_PAGINATION_SHOW_TOP => true,
            tubepress_app_api_template_VariableNames::GALLERY_PAGINATION_SHOW_BOTTOM => true,
        );
        $event = $this->mock('tubepress_lib_api_event_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($initial);
        $event->shouldReceive('getArgument')->with('pageNumber')->andReturn(1);
        $event->shouldReceive('getArgument')->with('page')->andReturn($this->_providerResult);
        $event->shouldReceive('setSubject')->once()->with(array_merge($initial, $final));

        $this->_sut->onGalleryTemplatePreRender($event);

        $this->assertTrue(true);
    }
}