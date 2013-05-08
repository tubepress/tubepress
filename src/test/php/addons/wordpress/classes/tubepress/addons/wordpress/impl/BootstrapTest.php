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
class tubepress_addons_wordpress_impl_BootstrapTest extends tubepress_test_TubePressUnitTest
{
    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEventDispatcher;

    /**
     * @var ehough_mockery_mockery_MockInterface
     */
    private $_mockEnvironmentDetector;

    /**
     * @var tubepress_addons_wordpress_impl_Bootstrap
     */
    private $_sut;

    public function onSetup()
    {
        $this->_mockEventDispatcher     = $this->createMockSingletonService(tubepress_api_event_EventDispatcherInterface::_);
        $this->_mockEnvironmentDetector = $this->createMockSingletonService(tubepress_spi_environment_EnvironmentDetector::_);

        $this->_sut = new tubepress_addons_wordpress_impl_Bootstrap();
    }

    public function testBoot()
    {
        $this->_mockEnvironmentDetector->shouldReceive('isWordPress')->once()->andReturn(true);

        $this->_mockEventDispatcher->shouldReceive('addListenerService')->once()->with(

            tubepress_api_const_event_EventNames::BOOT_COMPLETE,
            array('tubepress_addons_wordpress_impl_listeners_boot_WordPressOptionsRegistrar', 'onBoot')
        );

        $this->_mockEventDispatcher->shouldReceive('addListenerService')->once()->with(

            tubepress_api_const_event_EventNames::BOOT_COMPLETE,
            array('tubepress_addons_wordpress_impl_listeners_boot_WordPressApiIntegrator', 'onBoot')
        );

        $this->_mockEventDispatcher->shouldReceive('addListenerService')->once()->with(

            tubepress_api_const_event_EventNames::TEMPLATE_OPTIONS_UI_MAIN,
            array('tubepress_addons_wordpress_impl_listeners_template_options_OptionsUiTemplateListener', 'onOptionsUiTemplate')
        );

        $this->_sut->boot();

        $this->assertTrue(true);
    }
}