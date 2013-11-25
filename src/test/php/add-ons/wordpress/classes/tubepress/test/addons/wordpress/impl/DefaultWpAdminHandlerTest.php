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
class tubepress_test_addons_wordpress_impl_DefaultWpAdminHandlerTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_DefaultWpAdminHandler
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
        $this->_sut = new tubepress_addons_wordpress_impl_DefaultWpAdminHandler();

        $this->_mockWpFunctionWrapper           = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);
        $this->_mockHttpRequestParameterService = $this->createMockSingletonService(tubepress_spi_http_HttpRequestParameterService::_);
        $this->_mockStorageManager              = $this->createMockSingletonService(tubepress_spi_options_StorageManager::_);
        $this->_mockFormHandler                 = $this->createMockSingletonService('tubepress_spi_options_ui_OptionsPageInterface');
        $this->_mockEnvironmentDetector         = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
    }

    public function onTearDown()
    {
        unset($_SERVER['REQUEST_METHOD']);
        unset($_SERVER['HTTP_USER_AGENT']);
    }

    public function testMetaRowLinks()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('plugin_basename')->once()->with('tubepress/tubepress.php')->andReturn('xyz');
        $this->_mockWpFunctionWrapper->shouldReceive('__')->once()->with('Settings', 'tubepress')->andReturn('aaa');

        $result = $this->_sut->modifyMetaRowLinks(array(), 'xyz');

        $expected = array(

            '<a href="options-general.php?page=tubepress.php">aaa</a>',
            '<a href="http://tubepress.com/documentation/">Documentation</a>',
            '<a href="http://tubepress.com/forum/">Support</a>',
        );

        $this->assertEquals($expected, $result);
    }

    public function testAdminHeadMeta()
    {
        ob_start();
        $this->_sut->printHeadMeta();
        $result = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('<meta name="viewport" content="width=device-width, initial-scale=1.0"><meta http-equiv="X-UA-Compatible" content="IE=edge">', $result);
    }

    public function testSubmitValidValue()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';

        $this->_mockHttpRequestParameterService->shouldReceive('hasParam')->once()->with('tubepress_save')->andReturn(true);

        $this->_mockFormHandler->shouldReceive('getHtml')->once()->with(array(), true)->andReturn('yo');
        $this->_mockFormHandler->shouldReceive('onSubmit')->once()->andReturn(array());

        ob_start();

        $this->_sut->printOptionsPageHtml();

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
        $this->_sut->printOptionsPageHtml();
        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('yo', $contents);
    }

    public function testDisplayOptionsPage()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';

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

    public function testStylesAndScriptsDefault()
    {
        $this->_testRegisterStylesAndScripts();
    }

    public function testStylesAndScriptsIE7()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'MSIE 7.4;';

        $this->_testRegisterStylesAndScripts(true);
    }

    public function testStylesAndScriptsIE8()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'MSIE 8.4;';

        $this->_testRegisterStylesAndScripts(true);
    }

    public function testStylesAndScriptsIE9()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'MSIE 9.4;';

        $this->_testRegisterStylesAndScripts(false);
    }

    public function testStylesAndScriptsIE10()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'MSIE 10.4;';

        $this->_testRegisterStylesAndScripts(false);
    }

    private function _testRegisterStylesAndScripts($ie8orLess = false)
    {
        foreach ($this->_getCssMap() as $id => $path) {

            $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress' . $path, 'tubepress')->andReturn(strtoupper($id));
            $this->_mockWpFunctionWrapper->shouldReceive('wp_register_style')->once()->with($id, strtoupper($id));
            $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with($id);
        }

        foreach ($this->_getJsMap($ie8orLess) as $id => $path) {

            $this->_mockWpFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress' . $path, 'tubepress')->andReturn(strtoupper($id));
            $this->_mockWpFunctionWrapper->shouldReceive('wp_register_script')->once()->with($id, strtoupper($id));
            $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with($id, false, array(), false, false);
        }

        $this->_sut->registerStylesAndScripts('settings_page_tubepress');

        $this->assertTrue(true);
    }

    private function _getCssMap()
    {
        return array(

            'bootstrap-3.0.2'       => '/src/main/web/options-gui/vendor/bootstrap-3.0.2/css/bootstrap-custom.css',
            'bootstrap-theme'       => '/src/main/web/options-gui/vendor/bootstrap-3.0.2/css/bootstrap-custom-theme.css',
            'bootstrap-multiselect' => '/src/main/web/options-gui/vendor/bootstrap-multiselect-0.9.1/css/bootstrap-multiselect.css',
            'tubepress-extra'       => '/src/main/php/add-ons/wordpress/web/options-gui/css/options-page.css',
            'spectrum'              => '/src/main/web/options-gui/vendor/spectrum-1.1.2/spectrum.css',
        );
    }

    private function _getJsMap($ie8orLess)
    {
        $toReturn = array(

            'bootstrap-3.0.2' => '/src/main/web/options-gui/vendor/bootstrap-3.0.2/js/bootstrap.min.js',
        );

        if ($ie8orLess) {

            $toReturn = array_merge($toReturn, array(

                'html5-shiv-3.7.0' => '/src/main/web/options-gui/vendor/html5-shiv-3.7.0/html5shiv.js',
                'respond-1.3.0'    => '/src/main/web/options-gui/vendor/respond-1.3.0/respond.min.js',
            ));
        }

        $toReturn = array_merge($toReturn, array(

            'bootstrap-multiselect'         => '/src/main/web/options-gui/vendor/bootstrap-multiselect-0.9.1/js/bootstrap-multiselect.js',
            'spectrum'                      => '/src/main/web/options-gui/vendor/spectrum-1.1.2/spectrum.js',
            'bootstrap-field-error-handler' => '/src/main/web/options-gui/js/bootstrap-field-error-handler.js',
            'participant-filter-handler'    => '/src/main/web/options-gui/js/participant-filter-handler.js',
            'spectrum-js-initializer'       => '/src/main/web/options-gui/js/spectrum-js-initializer.js',
            'bootstrap-multiselect-init'    => '/src/main/web/options-gui/js/bootstrap-multiselect-initializer.js',
            'iframe-loader'                 => '/src/main/php/add-ons/wordpress/web/options-gui/js/iframe-loader.js',
        ));

        return $toReturn;
    }
}