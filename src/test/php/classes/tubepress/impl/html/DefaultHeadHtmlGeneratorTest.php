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
class tubepress_impl_html_DefaultHeadHtmlGeneratorTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockHttpRequestParameterService;

    function onSetup()
    {
        global $tubepress_base_url;

        $tubepress_base_url = '<tubepress_base_url>';

        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_sut = new tubepress_impl_html_DefaultHeadHtmlGenerator();
    }

    function onTearDown()
    {
        global $tubepress_base_url;

        unset($tubepress_base_url);
    }

    function testJqueryInclude()
    {
        $this->assertEquals('<script type="text/javascript" src="<tubepress_base_url>/src/main/web/js/jquery-1.8.3.min.js"></script>', $this->_sut->getHeadJqueryInclusion());
    }

    function testJsInclude()
    {
        $this->assertEquals('<script type="text/javascript" src="<tubepress_base_url>/src/main/web/js/tubepress.js"></script>', $this->_sut->getHeadJsIncludeString());
    }

    function testInlineJs()
    {
        $mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

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
