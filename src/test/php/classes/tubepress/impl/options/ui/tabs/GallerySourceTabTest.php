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
class tubepress_impl_options_ui_tabs_GallerySourceTabTest extends TubePressUnitTest
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

        $this->_sut = new tubepress_impl_options_ui_tabs_GallerySourceTab();
    }

    public function testGetName()
    {
        $this->assertEquals('<<message: Which videos?>>', $this->_sut->getTitle());
    }


}