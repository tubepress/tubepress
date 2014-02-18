<?php
/**
 * Copyright 2006 - 2014 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_addons_wordpress_impl_filters_Content
 */
class tubepress_test_addons_wordpress_impl_filters_ContentTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_filters_Content
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockShortcodeParser;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockShortcodeHtmlGenerator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockExecutionContext;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockMessageService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_filters_Content();

        $this->_mockExecutionContext       = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);
        $this->_mockMessageService         = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockShortcodeHtmlGenerator = $this->createMockSingletonService(tubepress_spi_shortcode_ShortcodeHtmlGenerator::_);
        $this->_mockShortcodeParser        = $this->createMockSingletonService(tubepress_spi_shortcode_ShortcodeParser::_);
        $this->_mockStorageManager         = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockEventDispatcher        = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
    }

    public function testNormalOperation()
    {
        $this->_mockStorageManager->shouldReceive('fetch')->once()->with(tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('trigger word');

        $this->_mockShortcodeParser->shouldReceive('somethingToParse')->times(2)->with('the content', 'trigger word')->andReturn(true);
        $this->_mockShortcodeParser->shouldReceive('somethingToParse')->times(2)->with('html for shortcode', 'trigger word')->andReturn(true, false);

        $this->_mockShortcodeHtmlGenerator->shouldReceive('getHtmlForShortcode')->once()->with('the content')->andReturn('html for shortcode');
        $this->_mockShortcodeHtmlGenerator->shouldReceive('getHtmlForShortcode')->once()->with('html for shortcode')->andReturn('html for shortcode');

        $this->_mockExecutionContext->shouldReceive('getActualShortcodeUsed')->times(4)->andReturn('<current shortcode>');
        $this->_mockExecutionContext->shouldReceive('reset')->twice();

        $this->assertEquals('html for shortcode', $this->_sut->filter(array('the content')));
    }

    public function testErrorCondition()
    {
        $this->_mockStorageManager->shouldReceive('fetch')->once()->with(tubepress_api_const_options_names_Advanced::KEYWORD)->andReturn('trigger word');

        $this->_mockShortcodeParser->shouldReceive('somethingToParse')->times(2)->with('the content', 'trigger word')->andReturn(true);
        $this->_mockShortcodeParser->shouldReceive('somethingToParse')->once()->with('something bad happened', 'trigger word')->andReturn(false);

        $exception = new Exception('something bad happened');

        $this->_mockShortcodeHtmlGenerator->shouldReceive('getHtmlForShortcode')->once()->with('the content')->andThrow($exception);

        $this->_mockExecutionContext->shouldReceive('getActualShortcodeUsed')->times(2)->andReturn('<current shortcode>');
        $this->_mockExecutionContext->shouldReceive('reset')->once();

        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_api_const_event_EventNames::ERROR_EXCEPTION_CAUGHT, ehough_mockery_Mockery::on(function ($event) use ($exception) {

            return $event instanceof tubepress_api_event_EventInterface && $event->getArgument('message') === $exception->getMessage()
                && $event->getSubject() instanceof Exception;
        }));

        $this->assertEquals('something bad happened', $this->_sut->filter(array('the content')));
    }
}
