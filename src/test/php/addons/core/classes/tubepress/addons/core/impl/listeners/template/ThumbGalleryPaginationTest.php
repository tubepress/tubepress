<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.org)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_core_impl_listeners_template_ThumbGalleryPaginationTest extends tubepress_test_TubePressUnitTest
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

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_core_impl_listeners_template_ThumbGalleryPagination();

        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockExecutionContext            = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockQueryStringService          = $this->createMockSingletonService(tubepress_spi_querystring_QueryStringService::_);
        $messageService                         = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(4);

        $this->_providerResult = new tubepress_api_video_VideoGalleryPage();
        $this->_providerResult->setTotalResultCount(500);

        $this->_mockTemplate = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PAGINATION_TOP, 'pagination-html');
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PAGINATION_BOTTOM, 'pagination-html');

        $this->_mockQueryStringService->shouldReceive('getFullUrl')->once()->andReturn('http://tubepress.org');

        $messageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
           return "##$msg##";
        });

        $this->_mockEventDispatcher = $this->createMockSingletonService('ehough_tickertape_EventDispatcherInterface');
    }

    public function testNoAjaxHighPage()
    {

        $expectedHtml = '<div class="pagination"><a rel="nofollow" href="http://tubepress.org?tubepress_page=24" data-page="24">&laquo; ##prev##</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=1" data-page="1">1</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=2" data-page="2">2</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=24" data-page="24">24</a><span class="current">25</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=26" data-page="26">26</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=124" data-page="124">124</a> <a rel="nofollow" href="http://tubepress.org?tubepress_page=125" data-page="125">125</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=26" data-page="26">##next## &raquo;</a></div>
';

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_PAGINATION, ehough_mockery_Mockery::on(function ($arg) use ($expectedHtml) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $expectedHtml;

            if (!$good) {

                $this->fail('Expected ' . $expectedHtml . ' but got ' . $arg->getSubject());
            }

            $arg->setSubject('pagination-html');

            return $good;
        }));
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(25);

        $this->_test();
    }

    public function testNoAjaxMiddlePage()
    {
        $expectedHtml = '<div class="pagination"><a rel="nofollow" href="http://tubepress.org?tubepress_page=11" data-page="11">&laquo; ##prev##</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=1" data-page="1">1</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=2" data-page="2">2</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=11" data-page="11">11</a><span class="current">12</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=13" data-page="13">13</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=124" data-page="124">124</a> <a rel="nofollow" href="http://tubepress.org?tubepress_page=125" data-page="125">125</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=13" data-page="13">##next## &raquo;</a></div>
';

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_PAGINATION, ehough_mockery_Mockery::on(function ($arg) use ($expectedHtml) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $expectedHtml;

            if (!$good) {

                $this->fail('Expected ' . $expectedHtml . ' but got ' . $arg->getSubject());
            }

            $arg->setSubject('pagination-html');

            return $good;
        }));
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(12);

        $this->_test();
    }

    public function testNoAjax()
    {
        $expectedHtml = '<div class="pagination"><span class="current">1</span><a rel="nofollow" href="http://tubepress.org?tubepress_page=2" data-page="2">2</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=3" data-page="3">3</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=4" data-page="4">4</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=5" data-page="5">5</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=124" data-page="124">124</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=125" data-page="125">125</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=2" data-page="2">##next## &raquo;</a></div>
';

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::HTML_PAGINATION, ehough_mockery_Mockery::on(function ($arg) use ($expectedHtml) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $expectedHtml;

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
        $event = new tubepress_api_event_TubePressEvent($this->_mockTemplate);
        $event->setArguments(array(

            'page' => 1,
            'providerName' => 'provider-name',
            'videoGalleryPage' => $this->_providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($this->_mockTemplate, $event->getSubject());
    }

}