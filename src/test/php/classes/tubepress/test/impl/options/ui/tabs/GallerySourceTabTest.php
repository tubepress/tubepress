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
class tubepress_test_impl_options_ui_tabs_GallerySourceTabTest extends tubepress_test_TubePressUnitTest
{
    private $_mockFieldBuilder;

    private $_mockTemplateBuilder;

    private $_mockExecutionContext;

    public function onSetup()
    {
        $ms                             = $this->createMockSingletonService(tubepress_spi_message_MessageService::_);
        $this->_mockTemplateBuilder     = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');
        $this->_mockFieldBuilder        = $this->createMockSingletonService(tubepress_spi_options_ui_FieldBuilder::_);
        $this->_mockExecutionContext    = $this->createMockSingletonService(tubepress_spi_context_ExecutionContext::_);

        $ms->shouldReceive('_')->andReturnUsing( function ($key) {

            return "<<message: $key>>";
        });

        $this->_sut = new tubepress_impl_options_ui_tabs_GallerySourceTab('yello');
    }

    public function testGetName()
    {
        $this->assertEquals('<<message: Which videos?>>', $this->_sut->getTitle());
    }


}