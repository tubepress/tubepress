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
class tubepress_impl_addon_filters_embeddedhtml_PlayerJavaScriptApiTest extends TubePressUnitTest
{
    /**
     * @var tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

	public function onSetup()
	{
		$this->_sut = new tubepress_plugins_core_impl_filters_embeddedhtml_PlayerJavaScriptApi();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
	}

    public function testJsApiNotEnabled()
    {
        $this->_mockEnvironmentDetector->shouldReceive('isPro')->once()->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(false);

        $event = new tubepress_api_event_TubePressEvent('hello');

        $this->_sut->onEmbeddedHtml($event);

        $this->assertEquals('hello', $event->getSubject());
    }

	public function testJsApiEnabled()
	{
        $this->_mockEnvironmentDetector->shouldReceive('isPro')->once()->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);

        $event = new tubepress_api_event_TubePressEvent('hello id="tubepress-video-object-47773745" ');
        $event->setArgument('videoId', 'abc');

        $this->_sut->onEmbeddedHtml($event);

        $expected = <<<EOT
hello id="tubepress-video-object-47773745" <script type="text/javascript">
   var tubePressDomInjector = tubePressDomInjector || [], tubePressPlayerApi = tubePressPlayerApi || [];
       tubePressDomInjector.push(['loadPlayerApiJs']);
       tubePressPlayerApi.push(['register', 'tubepress-video-object-47773745' ]);
</script>
EOT;

	    $this->assertEquals($expected, $event->getSubject());
	}
}