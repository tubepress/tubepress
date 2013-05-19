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
class tubepress_addons_wordpress_impl_DefaultWpAdminHandlerTest extends tubepress_test_TubePressUnitTest
{
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
        $this->_sut = new tubepress_addons_wordpress_impl_DefaultWpAdminHandler();

        $this->_mockWpFunctionWrapper           = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockFormHandler                 = $this->createMockSingletonService(tubepress_spi_options_ui_FormHandler::_);
        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
    }


    public function testSubmitThrowsException()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $this->_mockFormHandler->shouldReceive('onSubmit')->once()->andThrow(new Exception('something!'));

        $this->_setupWorkingNonce();

        ob_start();

        $this->_sut->printOptionsPageHtml();

        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div id="message" class="error fade"><p><strong>something!</strong></p></div>yo', $contents);
    }

    public function testSubmitValidValue()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $this->_mockFormHandler->shouldReceive('onSubmit')->once()->andReturn(null);

        $this->_setupWorkingNonce();

        ob_start();

        $this->_sut->printOptionsPageHtml();

        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div class="updated tubepress-options-updated"><p><strong>Options updated</strong></p></div>yo', $contents);
    }

    public function testSubmitInvalidValue()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $this->_mockFormHandler->shouldReceive('onSubmit')->once()->andReturn(array('bad value!', 'another bad value!'));

        $this->_setupWorkingNonce();

        ob_start();
        $this->_sut->printOptionsPageHtml();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div id="message" class="error fade"><p><strong>bad value!<br />another bad value!</strong></p></div>yo', $contents);
    }

    public function testDisplayOptionsPage()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(false);

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');

        ob_start();
        $this->_sut->printOptionsPageHtml();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('yo', $contents);
    }

    public function testMenuAction()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('add_options_page')->once()->with('TubePress Options', 'TubePress', 'manage_options', 'tubepress', array($this->_sut, 'printOptionsPageHtml'));

        $this->_sut->registerAdminMenuItem();

        $this->assertTrue(true);
    }

    public function testInit()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("tubepress/src/main/web/vendor/jquery-ui/jquery-ui-flick-theme/jquery-ui-1.8.24.custom.css", "tubepress")->andReturn('y');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("tubepress/src/main/web/css/options-page.css", "tubepress")->andReturn('z');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("tubepress/src/main/web/vendor/jquery-ui-multiselect-widget/jquery.multiselect.css", "tubepress")->andReturn('x');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("tubepress/src/main/web/vendor/jscolor/jscolor.js", "tubepress")->andReturn('a');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("tubepress/src/main/web/vendor/jquery-ui/jquery-ui-1.8.24.custom.min.js", "tubepress")->andReturn('b');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("tubepress/src/main/web/vendor/jquery-ui-multiselect-widget/jquery.multiselect.min.js", "tubepress")->andReturn('c');

        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_style')->once()->with("jquery-ui-flick", "y");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_style')->once()->with("tubepress-options-page", "z");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_style')->once()->with("jquery-ui-multiselect-widget", "x");

        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with("jquery-ui-flick");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with("tubepress-options-page");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with("jquery-ui-multiselect-widget");

        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_script')->once()->with("jscolor-tubepress", "a");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_script')->once()->with("jquery-ui-tubepress", "b");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_script')->once()->with("jquery-ui-multiselect-widget", "c");

        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jscolor-tubepress', false, array(), false, false);
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery-ui-tubepress', false, array(), false, false);
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery-ui-multiselect-widget', false, array(), false, false);

        $this->_sut->registerStylesAndScripts('settings_page_tubepress');

        $this->assertTrue(true);
    }

    private function _setupWorkingNonce()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('check_admin_referer')->once()->with('tubepress-save', 'tubepress-nonce')->andReturn(true);
    }

}