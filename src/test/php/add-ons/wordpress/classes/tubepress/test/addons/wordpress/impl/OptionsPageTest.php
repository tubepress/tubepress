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
 * @covers tubepress_addons_wordpress_impl_OptionsPage
 */
class tubepress_test_addons_wordpress_impl_OptionsPageTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_OptionsPage
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWpFunctionWrapper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHttpRequestParameterService;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockStorageManager;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockFormHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_OptionsPage();

        $this->_mockWpFunctionWrapper           = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockFormHandler                 = $this->createMockSingletonService('tubepress_spi_options_ui_OptionsPageInterface');
        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_api_environment_EnvironmentInterface::_);
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

        $this->_sut->run();

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
        $this->_sut->run();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('yo', $contents);
    }

    public function testDisplayOptionsPage()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');

        ob_start();
        $this->_sut->run();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('yo', $contents);
    }
}