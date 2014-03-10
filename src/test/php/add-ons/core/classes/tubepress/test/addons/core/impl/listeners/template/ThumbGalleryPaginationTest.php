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
 * @covers tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination
 */
class tubepress_test_addons_core_impl_listeners_template_ThumbGalleryPaginationTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination
     */
    private $_sut;

    /**
     * @var tubepress_api_video_VideoGalleryPage
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
    private $_mockQueryStringService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockThemeHandler;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination();

        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockQueryStringService          = $this->createMockSingletonService(tubepress_spi_querystring_QueryStringService::_);
        $messageService                         = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockEventDispatcher             = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockThemeHandler                = $this->createMockSingletonService(tubepress_spi_theme_ThemeHandlerInterface::_);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(4);

        $this->_providerResult = new tubepress_api_video_VideoGalleryPage();
        $this->_providerResult->setTotalResultCount(500);

        $this->_mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PAGINATION_TOP, 'pagination-html');
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PAGINATION_BOTTOM, 'pagination-html');

        $this->_mockQueryStringService->shouldReceive('getFullUrl')->once()->andReturn('http://tubepress.com');

        $messageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
           return "##$msg##";
        });
    }

    public function testModern()
    {
        $mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->twice()->with('pagination.tpl.php', '')->andReturn($mockTemplate);
        $expectedTemplateVars = array(

            tubepress_api_const_template_Variable::PAGINATION_CURRENT_PAGE     => 25,
            tubepress_api_const_template_Variable::PAGINATION_TOTAL_ITEMS      => 500,
            tubepress_api_const_template_Variable::PAGINATION_HREF_FORMAT      => '/?tubepress_page=%d',
            tubepress_api_const_template_Variable::PAGINATION_RESULTS_PER_PAGE => 4,
            tubepress_api_const_template_Variable::PAGINATION_TEXT_NEXT        => '##next##',
            tubepress_api_const_template_Variable::PAGINATION_TEXT_PREV        => '##prev##',
        );

        foreach ($expectedTemplateVars as $name => $val) {

            $mockTemplate->shouldReceive('setVariable')->once()->with($name, $val);
        }
        $mockTemplate->shouldReceive('toString')->once()->andReturn('foo');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::TEMPLATE_PAGINATION, ehough_mockery_Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === $mockTemplate;
        }));
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_PAGINATION, ehough_mockery_Mockery::on(function ($arg) {

            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === 'foo';

            if (!$good) {

                $this->fail('Expected foo but got ' . $arg->getSubject());
            }

            $arg->setSubject('pagination-html');

            return $good;
        }));
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(25);

        $this->_test();
    }

    public function testLegacyHighPage()
    {
        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('pagination.tpl.php', '')->andThrow(new InvalidArgumentException());
        $expectedHtml = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/fixtures/addons/core/pagination/legacy-high.html');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_PAGINATION, ehough_mockery_Mockery::on(function ($arg) use ($expectedHtml) {

            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === $expectedHtml;

            if (!$good) {

                $this->fail('Expected ' . $expectedHtml . ' but got ' . $arg->getSubject());
            }

            $arg->setSubject('pagination-html');

            return $good;
        }));
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(25);

        $this->_test();
    }

    public function testLegacyMiddlePage()
    {
        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('pagination.tpl.php', '')->andThrow(new InvalidArgumentException());
        $expectedHtml = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/fixtures/addons/core/pagination/legacy-middle.html');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_PAGINATION, ehough_mockery_Mockery::on(function ($arg) use ($expectedHtml) {

            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === $expectedHtml;

            if (!$good) {

                $this->fail('Expected ' . $expectedHtml . ' but got ' . $arg->getSubject());
            }

            $arg->setSubject('pagination-html');

            return $good;
        }));
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(12);

        $this->_test();
    }

    public function testLegacy()
    {
        $this->_mockThemeHandler->shouldReceive('getTemplateInstance')->once()->with('pagination.tpl.php', '')->andThrow(new InvalidArgumentException());
        $expectedHtml = file_get_contents(TUBEPRESS_ROOT . '/src/test/resources/fixtures/addons/core/pagination/legacy-low.html');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_PAGINATION, ehough_mockery_Mockery::on(function ($arg) use ($expectedHtml) {

            $good = $arg instanceof tubepress_api_event_EventInterface && $arg->getSubject() === $expectedHtml;

            if (!$good) {

                $this->fail('Expected ' . $expectedHtml . ' but got ' . $arg->getSubject());
            }

            $arg->setSubject('pagination-html');

            return $good;
        }));
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);

        $this->_test();
    }

    private function _test()
    {
        $event = new tubepress_spi_event_EventBase($this->_mockTemplate);
        $event->setArguments(array(

            'page'             => 1,
            'providerName'     => 'provider-name',
            'videoGalleryPage' => $this->_providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($this->_mockTemplate, $event->getSubject());
    }
}