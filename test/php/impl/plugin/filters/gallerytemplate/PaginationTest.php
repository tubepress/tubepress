<?php

require_once dirname(__FILE__) . '/../../../../../../sys/classes/org/tubepress/impl/plugin/filters/gallerytemplate/Pagination.class.php';

class org_tubepress_impl_plugin_filters_gallerytemplate_PaginationTest extends TubePressUnitTest
{
    private $_prefix = '<div class="pagination"><span class="current">1</span><a rel=';

    private $_sut;

    private $_providerResult;

    private $_mockTemplate;

    private $_execContext;

    private $_pluginManager;

    private $_qss;

    private $_hrps;

    public function setup()
    {
        parent::setUp();
        $this->_sut = new org_tubepress_impl_plugin_filters_gallerytemplate_Pagination();

        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        $this->_hrps = $ioc->get(org_tubepress_api_http_HttpRequestParameterService::_);

        $this->_execContext = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $this->_execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::PAGINATE_ABOVE)->andReturn(true);
        $this->_execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::PAGINATE_BELOW)->andReturn(true);
        $this->_execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::RESULTS_PER_PAGE)->andReturn(4);

        $this->_providerResult = \Mockery::mock('org_tubepress_api_provider_ProviderResult');
        $this->_providerResult->shouldReceive('getEffectiveTotalResultCount')->once()->andReturn(500);

        $this->_mockTemplate = \Mockery::mock('org_tubepress_api_template_Template');
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::PAGINATION_TOP, 'pagination-html');
        $this->_mockTemplate->shouldReceive('setVariable')->once()->with(org_tubepress_api_const_template_Variable::PAGINATION_BOTTOM, 'pagination-html');

        $this->_qss = $ioc->get(org_tubepress_api_querystring_QueryStringService::_);
        $this->_qss->shouldReceive('getFullUrl')->once()->andReturn('http://tubepress.org');

        $messageService = $ioc->get(org_tubepress_api_message_MessageService::_);
        $messageService->shouldReceive('_')->atLeast()->once()->andReturnUsing(function ($msg) {
           return "##$msg##";
        });

        $this->_pluginManager = $ioc->get(org_tubepress_api_plugin_PluginManager::_);
    }

    public function testAjax()
    {
        $expectedHtml = $this->_prefix . '"page=2">2</a><a rel="page=3">3</a><a rel="page=4">4</a><a rel="page=5">5</a><span class="tubepress_pagination_dots">...</span> <a rel="page=124">124</a><a rel="page=125">125</a><a rel="page=2">##next## &raquo;</a></div>
';

        $this->_pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_PAGINATION, $expectedHtml)->andReturn('pagination-html');
        $this->_execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(true);

        $this->_hrps->shouldReceive('getParamValueAsInt')->once()->with(org_tubepress_api_const_http_ParamName::PAGE, 1)->andReturn(1);

        $this->_test();
    }

    public function testNoAjaxHighPage()
    {

        $expectedHtml = '<div class="pagination"><a rel="nofollow" href="http://tubepress.org?tubepress_page=24">&laquo; ##prev##</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=1">1</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=2">2</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=24">24</a><span class="current">25</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=26">26</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=124">124</a> <a rel="nofollow" href="http://tubepress.org?tubepress_page=125">125</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=26">##next## &raquo;</a></div>
';

        $this->_pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_PAGINATION, $expectedHtml)->andReturn('pagination-html');
        $this->_execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(false);
        $this->_hrps->shouldReceive('getParamValueAsInt')->once()->with(org_tubepress_api_const_http_ParamName::PAGE, 1)->andReturn(25);

        $this->_test();
    }

    public function testNoAjaxMiddlePage()
    {
        $expectedHtml = '<div class="pagination"><a rel="nofollow" href="http://tubepress.org?tubepress_page=11">&laquo; ##prev##</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=1">1</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=2">2</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=11">11</a><span class="current">12</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=13">13</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=124">124</a> <a rel="nofollow" href="http://tubepress.org?tubepress_page=125">125</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=13">##next## &raquo;</a></div>
';

        $this->_pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_PAGINATION, $expectedHtml)->andReturn('pagination-html');
        $this->_execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(false);
        $this->_hrps->shouldReceive('getParamValueAsInt')->once()->with(org_tubepress_api_const_http_ParamName::PAGE, 1)->andReturn(12);

        $this->_test();
    }

    public function testNoAjax()
    {
        $expectedHtml = '<div class="pagination"><span class="current">1</span><a rel="nofollow" href="http://tubepress.org?tubepress_page=2">2</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=3">3</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=4">4</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=5">5</a><span class="tubepress_pagination_dots">...</span> <a rel="nofollow" href="http://tubepress.org?tubepress_page=124">124</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=125">125</a><a rel="nofollow" href="http://tubepress.org?tubepress_page=2">##next## &raquo;</a></div>
';

        $this->_pluginManager->shouldReceive('runFilters')->once()->with(org_tubepress_api_const_plugin_FilterPoint::HTML_PAGINATION, $expectedHtml)->andReturn('pagination-html');
        $this->_execContext->shouldReceive('get')->once()->with(org_tubepress_api_const_options_names_Thumbs::AJAX_PAGINATION)->andReturn(false);
        $this->_hrps->shouldReceive('getParamValueAsInt')->once()->with(org_tubepress_api_const_http_ParamName::PAGE, 1)->andReturn(1);

        $this->_test();
    }

    private function _test()
    {
        $this->assertEquals($this->_mockTemplate, $this->_sut->alter_galleryTemplate($this->_mockTemplate, $this->_providerResult, 1, 'provider-name'));
    }
}