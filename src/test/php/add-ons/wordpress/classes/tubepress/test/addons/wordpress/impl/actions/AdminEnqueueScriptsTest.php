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
 * @covers tubepress_addons_wordpress_impl_actions_AdminEnqueueScripts
 */
class tubepress_test_addons_wordpress_impl_actions_AdminEnqueueScriptsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_addons_wordpress_impl_actions_AdminEnqueueScripts
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_actions_AdminEnqueueScripts();

        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpFunctionsInterface::_);
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

            $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress' . $path, 'tubepress')->andReturn(strtoupper($id));
            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_style')->once()->with($id, strtoupper($id));
            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with($id);
        }

        foreach ($this->_getJsMap($ie8orLess) as $id => $path) {

            $this->_mockWordPressFunctionWrapper->shouldReceive('plugins_url')->once()->with('tubepress' . $path, 'tubepress')->andReturn(strtoupper($id));
            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_register_script')->once()->with($id, strtoupper($id));
            $this->_mockWordPressFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with($id, false, array(), false, false);
        }

        $this->_sut->execute(array('settings_page_tubepress'));

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
