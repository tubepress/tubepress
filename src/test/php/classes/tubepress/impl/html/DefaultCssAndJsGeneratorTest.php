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
class tubepress_impl_html_DefaultCssAndJsGeneratorTest extends TubePressUnitTest
{
    /**
     * @var tubepress_impl_html_DefaultCssAndJsGenerator
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    public function onSetup()
    {
        global $tubepress_base_url;

        $tubepress_base_url = '<tubepress_base_url>';

        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);

        $this->_sut = new tubepress_impl_html_DefaultCssAndJsGenerator();
    }

    public function onTearDown()
    {
        global $tubepress_base_url;

        unset($tubepress_base_url);
    }

    public function testJqueryInclude()
    {
        $this->assertEquals('<script type="text/javascript" src="<tubepress_base_url>/src/main/web/vendor/jquery-1.8.3.min.js"></script>', $this->_sut->getJqueryScriptTag());
    }

    public function testJsInclude()
    {
        $this->assertEquals('<script type="text/javascript" src="<tubepress_base_url>/src/main/web/js/tubepress.js"></script>', $this->_sut->getTubePressScriptTag());
    }

    public function testInlineJs()
    {
        $this->assertEquals('', $this->_sut->getInlineJs());
    }

    public function testCss()
    {
        $this->assertEquals('<link rel="stylesheet" href="<tubepress_base_url>/src/main/web/css/tubepress.css" type="text/css" />', $this->_sut->getTubePressCssTag());
    }

	public function testHeadMetaPageOne()
	{
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(1);

	    $this->assertEquals('', $this->_sut->getMetaTags());
	}

    public function testHeadMetaPageTwo()
	{
        $this->_mockHttpRequestParameterService->shouldReceive('getParamValueAsInt')->once()->with(tubepress_spi_const_http_ParamName::PAGE, 1)->andReturn(2);

	    $this->assertEquals('<meta name="robots" content="noindex, nofollow" />', $this->_sut->getMetaTags());
	}
}
