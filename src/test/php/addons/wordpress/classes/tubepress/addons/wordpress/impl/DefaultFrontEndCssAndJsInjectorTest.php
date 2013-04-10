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
class tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjectorTest extends TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWpFunctionWrapper;

    private $_sut;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockHeadHtmlGenerator;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    public function onSetup()
    {
        $this->_sut = new tubepress_addons_wordpress_impl_DefaultFrontEndCssAndJsInjector();

        $this->_mockWpFunctionWrapper   = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);
        $this->_mockHeadHtmlGenerator   = $this->createMockSingletonService(tubepress_spi_html_CssAndJsGenerator::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
    }

    public function testHeadAction()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);

        $this->_mockHeadHtmlGenerator->shouldReceive('getHeadInlineJs')->once()->andReturn('inline js');
        $this->_mockHeadHtmlGenerator->shouldReceive('getMetaTags')->once()->andReturn('html meta');

        ob_start();

        $this->_sut->printInHtmlHead();

        $contents = ob_get_contents();
        ob_end_clean();

        $this->assertEquals('inline js
html meta', $contents);
    }

    public function testInitAction()
    {
        $this->_mockWpFunctionWrapper->shouldReceive('is_admin')->once()->andReturn(false);
        $this->_mockWpFunctionWrapper->shouldReceive('addons_url')->once()->with('tubepress/src/main/web/js/tubepress.js', 'tubepress')->andReturn('<tubepressjs>');
        $this->_mockWpFunctionWrapper->shouldReceive('addons_url')->once()->with('tubepress/src/main/web/css/tubepress.css', 'tubepress')->andReturn('<tubepresscss>');

        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_script')->once()->with('tubepress', '<tubepressjs>');
        $this->_mockWpFunctionWrapper->shouldReceive('wp_register_style')->once()->with('tubepress', '<tubepresscss>');

        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('tubepress', false, array(), false, false);
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_style')->once()->with('tubepress');
        $this->_mockWpFunctionWrapper->shouldReceive('wp_enqueue_script')->once()->with('jquery', false, array(), false, false);

        $this->_sut->registerStylesAndScripts();

        $this->assertTrue(true);
    }
}