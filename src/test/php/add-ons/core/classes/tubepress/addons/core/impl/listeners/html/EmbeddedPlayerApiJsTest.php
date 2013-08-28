<?php
/**
 * Copyright 2006 - 2013 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */
class tubepress_addons_core_impl_listeners_html_EmbeddedPlayerApiJsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_core_impl_listeners_html_EmbeddedPlayerApiJs
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
        $this->_sut = new tubepress_addons_core_impl_listeners_html_EmbeddedPlayerApiJs();

        $this->_mockExecutionContext = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
    }

    public function testJsApiNotEnabled()
    {
        $this->_mockEnvironmentDetector->shouldReceive('isPro')->once()->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(false);

        $event = new tubepress_spi_event_EventBase('hello');

        $this->_sut->onEmbeddedHtml($event);

        $this->assertEquals('hello', $event->getSubject());
    }

    public function testJsApiEnabled()
    {
        $this->_mockEnvironmentDetector->shouldReceive('isPro')->once()->andReturn(true);
        $this->_mockExecutionContext->shouldReceive('get')->once()->with(tubepress_api_const_options_names_Embedded::ENABLE_JS_API)->andReturn(true);

        $event = new tubepress_spi_event_EventBase('hello id="tubepress-video-object-47773745" ');
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