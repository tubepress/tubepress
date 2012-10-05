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
class tubepress_plugins_wordpress_impl_DefaultWpAdminHandlerTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockWpFunctionWrapper;

    private $_mockHttpRequestParameterService;

    private $_mockStorageManager;

    private $_mockFormHandler;

    private $_mockEnvironmentDetector;

    function setUp()
    {
        $this->_sut = new tubepress_plugins_wordpress_impl_DefaultWpAdminHandler();

        $this->_mockWpFunctionWrapper = Mockery::mock(tubepress_plugins_wordpress_spi_WordPressFunctionWrapper::_);
        $this->_mockHttpRequestParameterService = Mockery::mock(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockStorageManager  = Mockery::mock(tubepress_spi_options_StorageManager::_);
        $this->_mockFormHandler = Mockery::mock(tubepress_spi_options_ui_FormHandler::_);
        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);

        tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::setWordPressFunctionWrapper($this->_mockWpFunctionWrapper);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setHttpRequestParameterService($this->_mockHttpRequestParameterService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionStorageManager($this->_mockStorageManager);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionsUiFormHandler($this->_mockFormHandler);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);
    }


    function testSubmitThrowsException()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $this->_mockStorageManager->shouldReceive('init')->once();

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $this->_mockFormHandler->shouldReceive('onSubmit')->once()->andThrow(new Exception('something!'));

        $this->_setupWorkingNonce();

        ob_start();

        $this->_sut->printOptionsPageHtml();

        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div id="message" class="error fade"><p><strong>something!</strong></p></div>yo', $contents);
    }

    function testSubmitValidValue()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $this->_mockStorageManager->shouldReceive('init')->once();

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $this->_mockFormHandler->shouldReceive('onSubmit')->once()->andReturn(null);

        $this->_setupWorkingNonce();

        ob_start();

        $this->_sut->printOptionsPageHtml();

        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div id="message" class="updated fade"><p><strong>Options updated</strong></p></div>yo', $contents);
    }

    function testSubmitInvalidValue()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $this->_mockStorageManager->shouldReceive('init')->once();

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');
        $this->_mockFormHandler->shouldReceive('onSubmit')->once()->andReturn(array('bad value!', 'another bad value!'));

        $this->_setupWorkingNonce();

        ob_start();
        $this->_sut->printOptionsPageHtml();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<div id="message" class="error fade"><p><strong>bad value!<br />another bad value!</strong></p></div>yo', $contents);
    }

    function testDisplayOptionsPage()
    {
        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(false);

        $this->_mockStorageManager->shouldReceive('init')->once();

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->andReturn('yo');

        ob_start();
        $this->_sut->printOptionsPageHtml();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('yo', $contents);
    }

    function testMenuAction()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('add_options_page')->once()->with('TubePress Options', 'TubePress', 'manage_options', 'tubepress', array($this->_sut, 'printOptionsPageHtml'));

        $this->_sut->registerAdminMenuItem();

        $this->assertTrue(true);
    }

    function testInit()
    {
        $this->_mockEnvironmentDetector->shouldReceive('getTubePressInstallationDirectoryBaseName')->once()->andReturn('base_name');

        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("base_name/src/main/web/css/jquery-ui-flick/jquery-ui-1.8.16.custom.css", "base_name")->andReturn('y');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("base_name/src/main/web/css/options-page.css", "base_name")->andReturn('z');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("base_name/src/main/web/css/jquery-ui-multiselect-widget/jquery.multiselect.css", "base_name")->andReturn('x');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("base_name/src/main/web/js/jscolor/jscolor.js", "base_name")->andReturn('a');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("base_name/src/main/web/js/jquery-ui/jquery-ui-1.8.16.custom.min.js", "base_name")->andReturn('b');
        $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with("base_name/src/main/web/js/jquery-ui-multiselect-widget/jquery.multiselect.min.js", "base_name")->andReturn('c');

        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_style')->once()->with("jquery-ui-flick", "y");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_style')->once()->with("tubepress-options-page", "z");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_style')->once()->with("jquery-ui-multiselect-widget", "x");

        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with("jquery-ui-flick");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with("tubepress-options-page");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with("jquery-ui-multiselect-widget");

        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_script')->once()->with("jscolor-tubepress", "a");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_script')->once()->with("jquery-ui-tubepress", "b");
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_script')->once()->with("jquery-ui-multiselect-widget", "c");

        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jscolor-tubepress');
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery-ui-tubepress');
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery-ui-multiselect-widget');

        $this->_sut->registerStylesAndScripts('settings_page_tubepress');

        $this->assertTrue(true);
    }

    private function _setupWorkingNonce()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('check_admin_referer')->once()->with('tubepress-save', 'tubepress-nonce')->andReturn(true);
    }

}
