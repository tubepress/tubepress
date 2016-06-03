<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * @covers tubepress_wordpress_impl_listeners_options_ui_OptionsPageListener
 */
class tubepress_test_wordpress_impl_listeners_options_ui_OptionsPageListenerTest extends tubepress_api_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_listeners_options_ui_OptionsPageListener
     */
    private $_sut;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockWpFunctionWrapper;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockFormHandler;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var Mockery\MockInterface
     */
    private $_mockEvent;

    public function onSetup()
    {
        $this->_mockEvent                       = $this->mock('tubepress_api_event_EventInterface');
        $this->_mockWpFunctionWrapper           = $this->mock(tubepress_wordpress_impl_wp_WpFunctions::_);
        $this->_mockHttpRequestParameterService = $this->mock(tubepress_api_http_RequestParametersInterface::_);
        $this->_mockStorageManager              = $this->mock(tubepress_api_options_PersistenceInterface::_);
        $this->_mockFormHandler                 = $this->mock('tubepress_api_options_ui_FormInterface');
        $this->_mockEnvironmentDetector         = $this->mock(tubepress_api_environment_EnvironmentInterface::_);

        $this->_sut = new tubepress_wordpress_impl_listeners_options_ui_OptionsPageListener(

            $this->_mockFormHandler,
            $this->_mockHttpRequestParameterService
        );
    }

    public function onTearDown()
    {
        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['HTTP_USER_AGENT']);
    }

    public function testSubmitValidValue()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->with(array(), true)->andReturn('yo');
        $this->_mockFormHandler->shouldReceive('onSubmit')->once()->andReturn(array());

        ob_start();

        $this->_sut->run($this->_mockEvent);

        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('yo', $contents);
    }

    public function testSubmitInvalidValue()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $this->_mockFormHandler->shouldReceive('onSubmit')->once()->andReturn(array('bad value!', 'another bad value!'));

        ob_start();
        $this->_sut->run($this->_mockEvent);
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('yo', $contents);
    }

    public function testDisplayOptionsPage()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');

        ob_start();
        $this->_sut->run($this->_mockEvent);
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('yo', $contents);
    }
}
