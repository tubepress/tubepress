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
class tubepress_impl_html_DefaultHeadHtmlGeneratorTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockHttpRequestParameterService;

    function setUp()
    {
        global $tubepress_base_url;

        $tubepress_base_url = '<tubepress_base_url>';

        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);

        $this->_sut = new tubepress_impl_html_DefaultHeadHtmlGenerator();


    }

    function onTearDown()
    {
        global $tubepress_base_url;

        unset($tubepress_base_url);
    }

    function testJqueryInclude()
    {
        $this->assertEquals('<script type="text/javascript" src="<tubepress_base_url>/src/main/web/js/jquery-1.8.2.min.js"></script>', $this->_sut->getHeadJqueryInclusion());
    }

    function testJsInclude()
    {
        $this->assertEquals('<script type="text/javascript" src="<tubepress_base_url>/src/main/web/js/tubepress.js"></script>', $this->_sut->getHeadJsIncludeString());
    }

    function testInlineJs()
    {
        $mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($mockExecutionContext);

        $mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Advanced::HTTPS)->andReturn(false);

        $this->assertEquals('<script type="text/javascript">TubePressGlobalJsConfig = { baseUrl : "<tubepress_base_url>", https : false };</script>', $this->_sut->getHeadInlineJs());
    }

    function testCss()
    {
        $this->assertEquals('<link rel="stylesheet" href="<tubepress_base_url>/src/main/web/css/tubepress.css" type="text/css" />', $this->_sut->getHeadCssIncludeString());
    }

	function testHeadMetaPageOne()
	{
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);

	    $this->assertEquals('', $this->_sut->getHeadHtmlMeta());
	}

    function testHeadMetaPageTwo()
	{
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(2);

	    $this->assertEquals('<meta name="robots" content="noindex, nofollow" />', $this->_sut->getHeadHtmlMeta());
	}
}
