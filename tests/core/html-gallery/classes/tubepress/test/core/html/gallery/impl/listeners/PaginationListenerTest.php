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
 * @covers tubepress_core_html_gallery_impl_listeners_PaginationTemplateListener
 */
class tubepress_test_core_html_gallery_impl_listeners_template_PaginationListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_core_html_gallery_impl_listeners_PaginationTemplateListener
     */
    private $_sut;

    /**
     * @var tubepress_core_media_provider_api_Page
     */
    private $_providerResult;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplate;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockCurrentUrlService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeLibrary;

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
    private $_mockTemplateFactory;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockUrlUtils;

    public function onSetup()
    {
        $this->_mockExecutionContext            = $this->mock(tubepress_core_options_api_ContextInterface::_);
        $this->_mockCurrentUrlService           = $this->mock(tubepress_core_url_api_UrlFactoryInterface::_);
        $messageService                         = $this->mock(tubepress_core_translation_api_TranslatorInterface::_);
        $this->_mockEventDispatcher             = $this->mock(tubepress_core_event_api_EventDispatcherInterface::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_core_http_api_RequestParametersInterface::_);
        $this->_mockTemplateFactory             = $this->mock(tubepress_core_template_api_TemplateFactoryInterface::_);
        $this->_mockUrlUtils                    = $this->mock('tubepress_core_util_api_UrlUtilsInterface');
        $this->_mockThemeLibrary                = $this->mock(tubepress_core_theme_api_ThemeLibraryInterface::_);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_PAGINATE_ABOVE)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_html_gallery_api_Constants::OPTION_PAGINATE_BELOW)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_core_media_provider_api_Constants::OPTION_RESULTS_PER_PAGE)->andReturn(4);

        $this->_providerResult = new tubepress_core_media_provider_api_Page();
        $this->_providerResult->setTotalResultCount(500);

        $this->_mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');

        $this->_mockFullUrl = $this->mock('tubepress_core_url_api_UrlInterface');
        $this->_mockFullQuery = $this->mock('tubepress_core_url_api_QueryInterface');
        $this->_mockCurrentUrlService->shouldReceive('fromCurrent')->once()->andReturn($this->_mockFullUrl);

        $messageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
           return "##$msg##";
        });

        $this->_mockTemplate->shouldReceive('getVariables')->twice()->andReturn(array('x' => 'z'));
        $this->_mockTemplate->shouldReceive('setVariables')->once()->with(array(

            'x' => 'z',
            tubepress_core_template_api_const_VariableNames::PAGINATION_BOTTOM => 'pagination-html',
            tubepress_core_template_api_const_VariableNames::PAGINATION_TOP    => 'pagination-html'
        ));

        $this->_sut = new tubepress_core_html_gallery_impl_listeners_PaginationTemplateListener(
            
            $this->_mockExecutionContext,
            $this->_mockCurrentUrlService,
            $messageService,
            $this->_mockEventDispatcher, 
            $this->_mockHttpRequestParameterService,
            $this->_mockTemplateFactory,
            $this->_mockUrlUtils,
            $this->_mockThemeLibrary);

    }

    public function testModern()
    {
        $mockTemplate = $this->mock('tubepress_core_template_api_TemplateInterface');
        $this->_mockThemeLibrary->shouldReceive('getAbsolutePathToTemplate')->once()->with('pagination.tpl.php')->andReturn('good!');
        $this->_mockTemplateFactory->shouldReceive('fromFilesystem')->once()->with(array('pagination.tpl.php', TUBEPRESS_ROOT . '/core/themes/web/default/pagination.tpl.php'))->andReturn($mockTemplate);
        $expectedTemplateVars = array(

            tubepress_core_template_api_const_VariableNames::PAGINATION_CURRENT_PAGE     => 25,
            tubepress_core_template_api_const_VariableNames::PAGINATION_TOTAL_ITEMS      => 500,
            tubepress_core_template_api_const_VariableNames::PAGINATION_HREF_FORMAT      => 'foobar',
            tubepress_core_template_api_const_VariableNames::PAGINATION_RESULTS_PER_PAGE => 4,
            tubepress_core_template_api_const_VariableNames::PAGINATION_TEXT_NEXT        => '##next##',
            tubepress_core_template_api_const_VariableNames::PAGINATION_TEXT_PREV        => '##prev##',
        );

        foreach ($expectedTemplateVars as $k => $v) {

            $mockTemplate->shouldReceive('setVariable')->once()->with($k, $v);
        }

        $mockTemplate->shouldReceive('toString')->once()->andReturn('foo');

        $mockTemplateEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockTemplateEvent->shouldReceive('getSubject')->once()->andReturn($mockTemplate);
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($mockTemplate)->andReturn($mockTemplateEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_gallery_api_Constants::EVENT_TEMPLATE_PAGINATION, $mockTemplateEvent);

        $mockHtmlEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('pagination-html');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('foo')->andReturn($mockHtmlEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_gallery_api_Constants::EVENT_HTML_PAGINATION, $mockHtmlEvent);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(25);
        $this->_mockFullUrl->shouldReceive('getQuery')->once()->andReturn($this->_mockFullQuery);

        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 'zQ12KeYf2ixV2h7l230e81QyE7Z5C54r5468pzQ12KeYf2ixV2h7l230e81QyE7Z5C54r5468p');

        $this->_mockUrlUtils->shouldReceive('getAsStringWithoutSchemeAndAuthority')->once()->with($this->_mockFullUrl)->andReturn('foobar');

        $this->_test();
    }

    public function testLegacyHighPage()
    {
        $this->_mockThemeLibrary->shouldReceive('getAbsolutePathToTemplate')->once()->with('pagination.tpl.php')->andReturnNull();
        $expectedHtml = file_get_contents(TUBEPRESS_ROOT . '/tests/core/html-gallery/fixtures/pagination/pagination/legacy-high.html');

        $mockHtmlEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('pagination-html');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($expectedHtml)->andReturn($mockHtmlEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_gallery_api_Constants::EVENT_HTML_PAGINATION, $mockHtmlEvent);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(25);

        $this->_mockFullQuery->shouldReceive('remove')->once()->with('tubepress_page');
        $this->_mockFullQuery->shouldReceive('set')->twice()->with('tubepress_page', 24);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 1);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 2);
        $this->_mockFullQuery->shouldReceive('set')->twice()->with('tubepress_page', 26);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 124);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 125);
        $this->_mockFullUrl->shouldReceive('getQuery')->times(9)->andReturn($this->_mockFullQuery);

        $this->_mockUrlUtils->shouldReceive('getAsStringWithoutSchemeAndAuthority')->times(8)->with($this->_mockFullUrl)->andReturn('/foo.bar?hello=goodbye&something=el%21se');

        $this->_test();
    }

    public function testLegacyMiddlePage()
    {
        $this->_mockThemeLibrary->shouldReceive('getAbsolutePathToTemplate')->once()->with('pagination.tpl.php')->andReturnNull();
        $expectedHtml = file_get_contents(TUBEPRESS_ROOT . '/tests/core/html-gallery/fixtures/pagination/pagination/legacy-middle.html');

        $mockHtmlEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('pagination-html');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($expectedHtml)->andReturn($mockHtmlEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_gallery_api_Constants::EVENT_HTML_PAGINATION, $mockHtmlEvent);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(12);

        $this->_mockFullQuery->shouldReceive('remove')->once()->with('tubepress_page');
        $this->_mockFullQuery->shouldReceive('set')->twice()->with('tubepress_page', 11);
        $this->_mockFullQuery->shouldReceive('set')->twice()->with('tubepress_page', 13);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 1);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 2);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 124);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 125);
        $this->_mockFullUrl->shouldReceive('getQuery')->times(9)->andReturn($this->_mockFullQuery);
        $this->_mockUrlUtils->shouldReceive('getAsStringWithoutSchemeAndAuthority')->times(8)->with($this->_mockFullUrl)->andReturn('/foo.bar?hello=goodbye&something=el%21se');


        $this->_test();
    }

    public function testLegacy()
    {
        $this->_mockThemeLibrary->shouldReceive('getAbsolutePathToTemplate')->once()->with('pagination.tpl.php')->andReturnNull();
        $expectedHtml = file_get_contents(TUBEPRESS_ROOT . '/tests/core/html-gallery/fixtures/pagination/pagination/legacy-low.html');


        $mockHtmlEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockHtmlEvent->shouldReceive('getSubject')->once()->andReturn('pagination-html');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with($expectedHtml)->andReturn($mockHtmlEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_core_html_gallery_api_Constants::EVENT_HTML_PAGINATION, $mockHtmlEvent);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_core_http_api_Constants::PARAM_NAME_PAGE, 1)->andReturn(1);

        $this->_mockFullQuery->shouldReceive('remove')->once()->with('tubepress_page');
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 3);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 4);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 5);
        $this->_mockFullQuery->shouldReceive('set')->twice()->with('tubepress_page', 2);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 124);
        $this->_mockFullQuery->shouldReceive('set')->once()->with('tubepress_page', 125);
        $this->_mockFullUrl->shouldReceive('getQuery')->times(8)->andReturn($this->_mockFullQuery);

        $this->_mockUrlUtils->shouldReceive('getAsStringWithoutSchemeAndAuthority')->times(7)->with($this->_mockFullUrl)->andReturn('/foo.bar?hello=goodbye&something=el%21se');

        $this->_test();
    }

    private function _test()
    {
        $event = $this->mock('tubepress_core_event_api_EventInterface');
        $event->shouldReceive('getSubject')->once()->andReturn($this->_mockTemplate);
        $event->shouldReceive('getArgument')->with('pageNumber')->andReturn(1);
        $event->shouldReceive('getArgument')->with('page')->andReturn($this->_providerResult);

        $this->_sut->onGalleryTemplate($event);

        $this->assertTrue(true);
    }
}