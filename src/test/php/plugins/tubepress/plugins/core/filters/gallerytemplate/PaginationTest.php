<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * TubePress is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * TubePress is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
 *
 */
class tubepress_plugins_core_filters_gallerytemplate_PaginationTest extends TubePressUnitTest
{
    private $_prefix = '<div class="pagination"><span class="current">1</span><a rel=';

    private $_sut;

    private $_providerResult;

    private $_mockTemplate;

    private $_mockExecutionContext;

    private $_mockEventDispatcher;

    private $_mockQueryStringService;

    private $_mockHttpRequestParameterService;

    public function setup()
    {
        $this->_sut = new tubepress_plugins_core_filters_gallerytemplate_Pagination();

        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW)->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(4);

        $this->_providerResult = new tubepress_api_video_VideoGalleryPage();
        $this->_providerResult->setTotalResultCount(500);

        $this->_mockTemplate = \Mockery::mock('ehough_contemplate_api_Template');
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PAGINATION_TOP, 'pagination-html');
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(tubepress_api_const_template_Variable::PAGINATION_BOTTOM, 'pagination-html');

        $this->_mockQueryStringService = Mockery::mock(tubepress_spi_querystring_QueryStringService::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setQueryStringService($this->_mockQueryStringService);
        $this->_mockQueryStringService->shouldReceive('getFullUrl')->once()->andReturn('http://tubepress.org');

        $messageService = Mockery::mock(tubepress_spi_message_MessageService::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($messageService);

        $messageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
           return "##$msg##";
        });

        $this->_mockEventDispatcher = Mockery::mock('ehough_tickertape_api_IEventDispatcher');
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);
    }

    public function testAjax()
    {
        $expectedHtml = $this->_prefix . '"page=2">2</a><a rel="page=3">3</a><a rel="page=4">4</a><a rel="page=5">5</a><span class="tubepress_pagination_dots">...</span> <a rel="page=124">124</a><a rel="page=125">125</a><a rel="page=2">##next## &raquo;</a></div>
';

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::PAGINATION_HTML_CONSTRUCTION, Mockery::on(function ($arg) use ($expectedHtml) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $expectedHtml;

            $arg->setSubject('pagination-html');

            return $good;
        }));

        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(true);

        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);

        $this->_test();
    }

    public function testNoAjaxHighPage()
    {

        $expectedHtml = '<div class="pagination"><a rel="nofollow" href="http://tubepress.org?tubepress_page=24">&laquo; ##prev##</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=1">1</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=2">2</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=24">24</a><span class="current">25</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=26">26</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=124">124</a> <a rel="nofollow" href="http://tubepress.org?tubepress_page=125">125</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=26">##next## &raquo;</a></div>
';

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::PAGINATION_HTML_CONSTRUCTION, Mockery::on(function ($arg) use ($expectedHtml) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $expectedHtml;

            $arg->setSubject('pagination-html');

            return $good;
        }));
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(false);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(25);

        $this->_test();
    }

    public function testNoAjaxMiddlePage()
    {
        $expectedHtml = '<div class="pagination"><a rel="nofollow" href="http://tubepress.org?tubepress_page=11">&laquo; ##prev##</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=1">1</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=2">2</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=11">11</a><span class="current">12</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=13">13</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=124">124</a> <a rel="nofollow" href="http://tubepress.org?tubepress_page=125">125</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=13">##next## &raquo;</a></div>
';

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::PAGINATION_HTML_CONSTRUCTION, Mockery::on(function ($arg) use ($expectedHtml) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $expectedHtml;

            $arg->setSubject('pagination-html');

            return $good;
        }));
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(false);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(12);

        $this->_test();
    }

    public function testNoAjax()
    {
        $expectedHtml = '<div class="pagination"><span class="current">1</span><a rel="nofollow" href="http://tubepress.org?tubepress_page=2">2</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=3">3</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=4">4</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=5">5</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=124">124</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=125">125</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=2">##next## &raquo;</a></div>
';

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_CoreEventNames::PAGINATION_HTML_CONSTRUCTION, Mockery::on(function ($arg) use ($expectedHtml) {

            $good = $arg instanceof tubepress_api_event_TubePressEvent && $arg->getSubject() === $expectedHtml;

            $arg->setSubject('pagination-html');

            return $good;
        }));
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(false);
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);

        $this->_test();
    }

    private function _test()
    {
        $event = new tubepress_api_event_ThumbnailGalleryTemplateConstruction($this->_mockTemplate);
        $event->setArguments(array(

            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_PAGE => 1,
            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_PROVIDER_NAME => 'provider-name',
            tubepress_api_event_ThumbnailGalleryTemplateConstruction::ARGUMENT_VIDEO_GALLERY_PAGE => $this->_providerResult
        ));

        $this->_sut->onGalleryTemplate($event);

        $this->assertEquals($this->_mockTemplate, $event->getSubject());
    }

}