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
abstract class tubepress_impl_embedded_commands_AbstractEmbeddedCommandTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockExecutionContext;

    private $_mockThemeHandler;

    public function setUp()
    {
        $this->_sut = $this->buildSut();

        $this->_mockExecutionContext = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockThemeHandler     = Mockery::mock(tubepress_spi_theme_ThemeHandler::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setThemeHandler($this->_mockThemeHandler);
    }

    protected function getMockThemeHandler()
    {
        return $this->_mockThemeHandler;
    }

    protected function getMockExecutionContext()
    {
        return $this->_mockExecutionContext;
    }

    protected function getSut()
    {
        return $this->_sut;
    }

    abstract function buildSut();
}

