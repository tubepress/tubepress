<?php
/**
 * Copyright 2006 - 2015 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_app_impl_html_HtmlGenerator
 */
class tubepress_test_app_impl_html_HtmlGeneratorTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_html_HtmlGenerator
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplating;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_cssAndJsGenerationHelper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironment;

    public function onSetup()
    {
        $this->_mockEventDispatcher      = $this->mock(tubepress_lib_api_event_EventDispatcherInterface::_);
        $this->_mockTemplating           = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);
        $this->_cssAndJsGenerationHelper = $this->mock('tubepress_app_impl_html_CssAndJsGenerationHelper');
        $this->_mockEnvironment          = $this->mock(tubepress_app_api_environment_EnvironmentInterface::_);

        $this->_sut = new tubepress_app_impl_html_HtmlGenerator(

            $this->_mockEventDispatcher,
            $this->_mockTemplating,
            $this->_cssAndJsGenerationHelper,
            $this->_mockEnvironment
        );
    }

    public function testHtmlBad()
    {
        $mockGenerationEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('')->andReturn($mockGenerationEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::HTML_GENERATION, $mockGenerationEvent);
        $mockGenerationEvent->shouldReceive('getSubject')->once()->andReturnNull();

        $mockErrorEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with(ehough_mockery_Mockery::type('RuntimeException'))->andReturn($mockErrorEvent);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::HTML_EXCEPTION_CAUGHT, $mockErrorEvent);

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('exception/static', ehough_mockery_Mockery::type('array'))->andReturn('abc');

        $actual = $this->_sut->getHtml();

        $this->assertEquals('abc', $actual);
    }

    public function testHtmlOK()
    {
        $mockGenerationEventPre = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('')->andReturn($mockGenerationEventPre);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::HTML_GENERATION, $mockGenerationEventPre);
        $mockGenerationEventPre->shouldReceive('getSubject')->once()->andReturn('abc');

        $mockGenerationEventPost = $this->mock('tubepress_lib_api_event_EventInterface');
        $this->_mockEventDispatcher->shouldReceive('newEventInstance')->once()->with('abc')->andReturn($mockGenerationEventPost);
        $this->_mockEventDispatcher->shouldReceive('dispatch')->once()->with(tubepress_app_api_event_Events::HTML_GENERATION_POST, $mockGenerationEventPost);
        $mockGenerationEventPost->shouldReceive('getSubject')->once()->andReturn('xyz');

        $actual = $this->_sut->getHtml();

        $this->assertEquals('xyz', $actual);
    }

    /**
     * @dataProvider delegation
     */
    public function testDelegation($method)
    {
        $this->_cssAndJsGenerationHelper->shouldReceive($method)->once()->andReturn('foo');

        $this->assertEquals('foo', $this->_sut->$method());
    }

    public function delegation()
    {
        return array(
            array('getUrlsJS'),
            array('getUrlsCSS'),
            array('getCSS'),
            array('getJS')
        );
    }
}
