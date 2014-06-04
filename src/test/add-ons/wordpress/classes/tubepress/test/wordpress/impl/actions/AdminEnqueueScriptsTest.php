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
 * @covers tubepress_wordpress_impl_actions_AdminEnqueueScripts
 */
class tubepress_test_wordpress_impl_actions_AdminEnqueueScriptsTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var tubepress_wordpress_impl_actions_AdminEnqueueScripts
     */
    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    public function onSetup()
    {
        $this->_mockWordPressFunctionWrapper = $this->mock(tubepress_wordpress_spi_WpFunctionsInterface::_);

        $this->_sut = new tubepress_wordpress_impl_actions_AdminEnqueueScripts($this->_mockWordPressFunctionWrapper);
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

        $mockEvent = $this->mock('tubepress_core_event_api_EventInterface');
        $mockEvent->shouldReceive('getSubject')->once()->andReturn(array('settings_page_tubepress'));
        $this->_sut->action($mockEvent);

        $this->assertTrue(true);
    }

    private function _getCssMap()
    {
        return array(

            'bootstrap-3.1.1'         => '/src/main/web/options-gui/vendor/bootstrap-3.1.1/css/bootstrap-custom.css',
            'bootstrap-theme'         => '/src/main/web/options-gui/vendor/bootstrap-3.1.1/css/bootstrap-custom-theme.css',
            'bootstrap-multiselect'   => '/src/main/web/options-gui/vendor/bootstrap-multiselect-0.9.2/css/bootstrap-multiselect.css',
            'blueimp-gallery-2.14.0'  => '/src/main/web/options-gui/vendor/blueimp-gallery-2.14.0/css/blueimp-gallery.min.css',
            'bootstrap-image-gallery' => '/src/main/web/options-gui/vendor/bootstrap-image-gallery-3.1.0/css/bootstrap-image-gallery.css',
            'tubepress-options-gui'   => '/src/main/web/options-gui/css/options-page.css',
            'wordpress-options-gui'   => '/src/main/php/add-ons/wordpress/web/options-gui/css/options-page.css',
            'spectrum'                => '/src/main/web/options-gui/vendor/spectrum-1.3.1/spectrum.css',
        );
    }

    private function _getJsMap($ie8orLess)
    {
        $toReturn = array(

            'bootstrap-3.1.1' => '/src/main/web/options-gui/vendor/bootstrap-3.1.1/js/bootstrap.min.js',
        );

        if ($ie8orLess) {

            $toReturn = array_merge($toReturn, array(

                'html5-shiv-3.7.0' => '/src/main/web/options-gui/vendor/html5-shiv-3.7.0/html5shiv.js',
                'respond-1.4.2'    => '/src/main/web/options-gui/vendor/respond-1.4.2/respond.min.js',
            ));
        }

        $toReturn = array_merge($toReturn, array(

            'bootstrap-multiselect'         => '/src/main/web/options-gui/vendor/bootstrap-multiselect-0.9.2/js/bootstrap-multiselect.js',
            'spectrum'                      => '/src/main/web/options-gui/vendor/spectrum-1.3.1/spectrum.js',
            'blueimp-gallery-2.14.0'        => '/src/main/web/options-gui/vendor/blueimp-gallery-2.14.0/js/blueimp-gallery.min.js',
            'bootstrap-image-gallery'       => '/src/main/web/options-gui/vendor/bootstrap-image-gallery-3.1.0/js/bootstrap-image-gallery.js',
            'bootstrap-field-error-handler' => '/src/main/web/options-gui/js/bootstrap-field-error-handler.js',
            'participant-filter-handler'    => '/src/main/web/options-gui/js/participant-filter-handler.js',
            'spectrum-js-initializer'       => '/src/main/web/options-gui/js/spectrum-js-initializer.js',
            'bootstrap-multiselect-init'    => '/src/main/web/options-gui/js/bootstrap-multiselect-initializer.js',
            'theme-field-handler'           => '/src/main/web/options-gui/js/theme-field-handler.js',
            'theme-reminder'                => '/src/main/php/add-ons/wordpress/web/options-gui/js/theme-reminder.js',
            'iframe-loader'                 => '/src/main/php/add-ons/wordpress/web/options-gui/js/iframe-loader.js',
        ));

        return $toReturn;
    }
}
