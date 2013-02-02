<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_impl_plugin_filters_embeddedhtml_PlayerJavaScriptApitTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi
     */
    private $_sut;

    private $_mockExecutionContext;

	function onSetup()
	{
		$this->_sut = new tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
	}

    function testJsApiNotEnabled()
    {
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(false);

        $event = new tubepress_api_event_TubePressEvent('hello');

        $this->_sut->onEmbeddedHtml($event);

        $this->assertEquals('hello', $event->getSubject());
    }

	function testJsApiEnabled()
	{
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);

        $event = new tubepress_api_event_TubePressEvent('hello');
        $event->setArgument('videoId', 'abc');

        $this->_sut->onEmbeddedHtml($event);

	    $this->assertEquals('hello<script type="text/javascript">TubePressPlayerApi.register(\'abc\');</script>', $event->getSubject());
	}
}