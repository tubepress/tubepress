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
 * @covers tubepress_app_impl_listeners_embedded_SourceListener<extended>
 */
class tubepress_test_app_impl_listeners_embedded_SourceListenerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_app_impl_listeners_embedded_SourceListener
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockTemplatingService;

    public function onSetup()
    {
        $this->_mockTemplatingService = $this->mock(tubepress_lib_api_template_TemplatingInterface::_);

        $this->_sut = new tubepress_app_impl_listeners_embedded_SourceListener(
            $this->_mockTemplatingService
        );
    }

    public function testGetHtml()
    {
        $mockEvent = $this->mock('tubepress_lib_api_event_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array(
            'a' => 'b'
        ));
        $mockEvent->shouldReceive('setSubject')->once()->with(array(
            'a' => 'b',
            tubepress_app_api_template_VariableNames::EMBEDDED_SOURCE => 'foo'
        ));

        $this->_mockTemplatingService->shouldReceive('renderTemplate')->once()->with('embed', array('a' => 'b'))->andReturn('foo');

        $this->_sut->addEmbeddedHtmlToTemplate($mockEvent);

        $this->assertTrue(true);
    }
}
