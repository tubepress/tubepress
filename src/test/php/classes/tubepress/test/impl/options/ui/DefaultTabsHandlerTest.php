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
class tubepress_test_impl_options_ui_DefaultTabsHandlerTest extends tubepress_test_TubePressUnitTest
{
    private $_sut;

    private $_mockTemplateBuilder;

    private $_expectedTabs = array();

    public function onSetup()
    {
        $this->_mockTemplateBuilder = $this->createMockSingletonService('ehough_contemplate_api_TemplateBuilder');

        for ($x = 0; $x < 8; $x++) {

            $this->_expectedTabs[] = $this->createMockPluggableService(tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME);
        }

        $this->_sut = new tubepress_impl_options_ui_DefaultTabsHandler();
    }

    public function testSubmitWithErrors()
    {
        $x = 1;
        foreach ($this->_expectedTabs as $tab) {

            $tab->shouldReceive('onSubmit')->once()->andReturn(array($x++));
        }

        $result = $this->_sut->onSubmit();

        $this->assertEquals(array(1, 2, 3, 4, 5, 6, 7, 8), $result);
    }

    public function testSubmit()
    {
        foreach ($this->_expectedTabs as $tab) {

            $tab->shouldReceive('onSubmit')->once();
        }

        $this->assertNull($this->_sut->onSubmit());
    }

    public function testGetHtml()
    {
        $template = ehough_mockery_Mockery::mock('ehough_contemplate_api_Template');
        $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_DefaultTabsHandler::TEMPLATE_VAR_TABS, $this->_expectedTabs);
        $template->shouldReceive('toString')->once()->andReturn('foobar');

        $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with(TUBEPRESS_ROOT . '/src/main/resources/system-templates/options_page/tabs.tpl.php')->andReturn($template);

        $this->assertEquals('foobar', $this->_sut->getHtml());
    }
}
