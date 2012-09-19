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
class tubepress_impl_options_ui_DefaultTabsHandlerTest extends TubePressUnitTest
{
	private $_sut;

    private $_mockTemplateBuilder;

    private $_mockEnvironmentDetector;

	private $_expectedTabs = array();

	public function setup()
	{
        $this->_mockTemplateBuilder     = Mockery::mock('ehough_contemplate_api_TemplateBuilder');
        $this->_mockEnvironmentDetector = Mockery::mock(tubepress_spi_environment_EnvironmentDetector::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setTemplateBuilder($this->_mockTemplateBuilder);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setEnvironmentDetector($this->_mockEnvironmentDetector);

        for ($x = 0; $x < 8; $x++) {

            $this->_expectedTabs[] = Mockery::mock(tubepress_spi_options_ui_FormHandler::_);
        }

		$this->_sut = new tubepress_impl_options_ui_DefaultTabsHandler($this->_expectedTabs);
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
	    $vals = array('one' => 'two');

	    foreach ($this->_expectedTabs as $tab) {

	        $tab->shouldReceive('onSubmit')->once();
	    }

	    $this->assertNull($this->_sut->onSubmit());
	}

	public function testGetHtml()
	{
	    $template = \Mockery::mock('ehough_contemplate_api_Template');
	    $template->shouldReceive('setVariable')->once()->with(tubepress_impl_options_ui_DefaultTabsHandler::TEMPLATE_VAR_TABS, $this->_expectedTabs);
	    $template->shouldReceive('toString')->once()->andReturn('foobar');

	    $this->_mockEnvironmentDetector->shouldReceive('getTubePressBaseInstallationPath')->once()->andReturn('<<basepath>>');

	    $this->_mockTemplateBuilder->shouldReceive('getNewTemplateInstance')->once()->with('<<basepath>>/src/main/resources/system-templates/options_page/tabs.tpl.php')->andReturn($template);

	    $this->assertEquals('foobar', $this->_sut->getHtml());
	}
}

