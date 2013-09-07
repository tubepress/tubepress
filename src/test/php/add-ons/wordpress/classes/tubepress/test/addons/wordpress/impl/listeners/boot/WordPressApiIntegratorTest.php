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
class tubepress_test_addons_wordpress_impl_listeners_boot_WordPressApiIntegratorTest extends tubepress_test_TubePressUnitTest
{

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWordPressFunctionWrapper;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockContentFilter;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockJsAndCssInjector;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWpAdminHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockWidgetHandler;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockWordPressFunctionWrapper = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WordPressFunctionWrapper::_);
        $this->_mockContentFilter            = $this->createMockSingletonService(tubepress_addons_wordpress_spi_ContentFilter::_);
        $this->_mockJsAndCssInjector         = $this->createMockSingletonService(tubepress_addons_wordpress_spi_FrontEndCssAndJsInjector::_);
        $this->_mockWpAdminHandler           = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WpAdminHandler::_);
        $this->_mockWidgetHandler            = $this->createMockSingletonService(tubepress_addons_wordpress_spi_WidgetHandler::_);
        $this->_mockEnvironmentDetector      = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);
        $this->_sut                          = new tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator();
    }

    public function testCore()
    {
        $this->_testApi();

        $this->_sut->onBoot(new tubepress_spi_event_EventBase());

        $this->assertTrue(true);
    }

    private function _testApi()
    {
        $this->_mockWordPressFunctionWrapper->shouldReceive('wp_version')->once()->andReturn('3.1');

        $this->_mockWordPressFunctionWrapper->shouldReceive('content_url')->once()->andReturn('valueofcontenturl');

        $this->_mockWordPressFunctionWrapper->shouldReceive('load_plugin_textdomain')->once()->with('tubepress', false, 'tubepress/src/main/resources/i18n');

        $this->_mockWordPressFunctionWrapper->shouldReceive('add_filter')->once()->with('the_content', array($this->_mockContentFilter, 'filterContent'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('wp_head', array($this->_mockJsAndCssInjector, 'printInHtmlHead'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('init', array($this->_mockJsAndCssInjector, 'registerStylesAndScripts'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('admin_menu', array($this->_mockWpAdminHandler, 'registerAdminMenuItem'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('admin_enqueue_scripts', array($this->_mockWpAdminHandler, 'registerStylesAndScripts'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('widgets_init', array($this->_mockWidgetHandler, 'registerWidget'), 10, 1);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_filter')->once()->with('plugin_row_meta', array($this->_mockWpAdminHandler, 'modifyMetaRowLinks'), 10, 2);
        $this->_mockWordPressFunctionWrapper->shouldReceive('add_action')->once()->with('admin_head', array($this->_mockWpAdminHandler, 'printHeadMeta'), 10, 1);

        $this->_mockWordPressFunctionWrapper->shouldReceive('register_activation_hook')->once()->with('tubepress/tubepress.php', array('tubepress_addons_wordpress_impl_Bootstrap', '__callbackEnsureTubePressContentDirectoryExists'));

        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(true);
        $this->_mockEnvironmentDetector->shouldReceive('setBaseUrl')->once()->with('valueofcontenturl/plugins/tubepress');
    }
}