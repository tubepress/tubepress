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
class org_tubepress_impl_embedded_EmbeddedPlayerChainTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockChain;

    private $_mockProviderCalculator;

    private $_mockTemplate;

    private $_mockEventDispatcher;

    public function setUp()
    {
        $this->_mockChain              = Mockery::mock('ehough_chaingang_api_Chain');
        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);
        $this->_mockTemplate           = Mockery::mock('ehough_contemplate_api_Template');
        $this->_mockEventDispatcher    = Mockery::mock('ehough_tickertape_api_IEventDispatcher');

        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator($this->_mockProviderCalculator);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEventDispatcher($this->_mockEventDispatcher);

        $this->_sut = new tubepress_impl_embedded_EmbeddedPlayerChain($this->_mockChain);
    }

    function testGetHtml()
    {
        $this->_mockProviderCalculator->shouldReceive('calculateProviderOfVideoId')->with('videoid')->once()->andReturn('video_provider');

        $mockTemplate = $this->_mockTemplate;
        $this->_mockChain->shouldReceive('execute')->once()->with(Mockery::on(function ($arg) use ($mockTemplate) {

            $arg->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_TEMPLATE, $mockTemplate);
            $arg->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_DATA_URL, 'data-url');
            $arg->put(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_IMPLEMENTATION_NAME, 'impl-name');

            return $arg instanceof ehough_chaingang_api_Context && $arg->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_PROVIDER_NAME) === 'video_provider'
                && $arg->get(tubepress_impl_embedded_EmbeddedPlayerChain::CHAIN_KEY_VIDEO_ID) === 'videoid';

        }))->andReturn(true);

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_EmbeddedTemplateConstruction::EVENT_NAME,
            Mockery::on(function ($arg) use ($mockTemplate) {

            return $arg instanceof tubepress_api_event_EmbeddedTemplateConstruction && $arg->getSubject() === $mockTemplate
                && $arg->getArgument(tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_VIDEO_ID) === 'videoid'
                && $arg->getArgument(tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_PROVIDER_NAME) === 'video_provider'
                && $arg->getArgument(tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_DATA_URL) === 'data-url'
                && $arg->getArgument(tubepress_api_event_EmbeddedTemplateConstruction::ARGUMENT_EMBEDDED_IMPLEMENTATION_NAME) === 'impl-name';
        }));

        $mockTemplate->shouldReceive('toString')->once()->andReturn('templateAsString');

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_event_EmbeddedHtmlConstruction::EVENT_NAME,
            Mockery::on(function ($arg) use ($mockTemplate) {

                return $arg instanceof tubepress_api_event_EmbeddedHtmlConstruction && $arg->getSubject() === 'templateAsString'
                    && $arg->getArgument(tubepress_api_event_EmbeddedHtmlConstruction::ARGUMENT_VIDEO_ID) === 'videoid'
                    && $arg->getArgument(tubepress_api_event_EmbeddedHtmlConstruction::ARGUMENT_PROVIDER_NAME) === 'video_provider'
                    && $arg->getArgument(tubepress_api_event_EmbeddedHtmlConstruction::ARGUMENT_DATA_URL) === 'data-url'
                    && $arg->getArgument(tubepress_api_event_EmbeddedHtmlConstruction::ARGUMENT_EMBEDDED_IMPLEMENTATION_NAME) === 'impl-name';
            }));

        $result = $this->_sut->getHtml('videoid');
        $this->assertEquals('templateAsString', $result);
    }

}
