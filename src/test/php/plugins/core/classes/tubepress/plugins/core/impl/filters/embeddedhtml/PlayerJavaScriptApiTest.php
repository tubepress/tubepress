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
class org_tubepress_impl_plugin_filters_embeddedhtml_PlayerJavaScriptApitTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi
     */
    private $_sut;

    private $_mockExecutionContext;

	function setup()
	{
		$this->_sut = new tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi();

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
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