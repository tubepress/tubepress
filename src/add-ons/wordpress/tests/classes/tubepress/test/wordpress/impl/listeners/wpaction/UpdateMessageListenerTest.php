<?php
/**
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_listeners_wpaction_UpdateMessageListener
 */
class tubepress_test_wordpress_impl_listeners_wpaction_UpdateMessageListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_wpaction_UpdateMessageListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockContext;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockTemplating;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockIncomingEvent;

    public function onSetup()
    {
        $this->_mockContext       = $this->mock(tubepress_api_options_ContextInterface::_);
        $this->_mockTemplating    = $this->mock(tubepress_api_template_TemplatingInterface::_);
        $this->_mockIncomingEvent = $this->mock('tubepress_api_event_EventInterface');

        $this->_sut = new tubepress_wordpress_impl_listeners_wpaction_UpdateMessageListener(

            $this->_mockContext,
            $this->_mockTemplating
        );
    }

    public function testInUpdateMessageKey()
    {
        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::TUBEPRESS_API_KEY)->andReturn('key');

        $this->_sut->onAction_in_plugin_update_message($this->_mockIncomingEvent);
    }

    public function testInUpdateMessageNoKey()
    {
        $this->expectOutputString('foobar');

        $this->_mockContext->shouldReceive('get')->once()->with(tubepress_api_options_Names::TUBEPRESS_API_KEY)->andReturnNull();

        $this->_mockTemplating->shouldReceive('renderTemplate')->once()->with('wordpress/in-update-message')->andReturn('foobar');

        $this->_sut->onAction_in_plugin_update_message($this->_mockIncomingEvent);
    }
}
