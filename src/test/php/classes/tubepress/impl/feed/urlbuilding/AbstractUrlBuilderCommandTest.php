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
abstract class tubepress_impl_feed_urlbuilding_AbstractUrlBuilderCommandTest extends TubePressUnitTest
{
    private $_sut;

    private $_mockExecutionContext;

    private $_mockProviderCalculator;

    function setUp()
    {
        $this->_sut = $this->buildSut();

        $this->_mockExecutionContext   = Mockery::mock(tubepress_spi_context_ExecutionContext::_);
        $this->_mockProviderCalculator = Mockery::mock(tubepress_spi_provider_ProviderCalculator::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setExecutionContext($this->_mockExecutionContext);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setVideoProviderCalculator($this->_mockProviderCalculator);
    }

    protected function expectOptions($opts)
    {
        foreach ($opts as $name => $value) {

            $this->_mockExecutionContext->shouldReceive('get')->zeroOrMoreTimes()->with($name)->andReturn($value);
        }
    }

    protected abstract function buildSut();

    protected function getMockExecutionContext()
    {
        return $this->_mockExecutionContext;
    }

    protected function getMockProviderCalculator()
    {
        return $this->_mockProviderCalculator;
    }

    protected function getSut()
    {
        return $this->_sut;
    }

    protected static function buildContext($provider, $single, $arg)
    {
        $context = new ehough_chaingang_impl_StandardContext();

        $context->put(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_ARGUMENT, $arg);
        $context->put(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_IS_SINGLE, $single);
        $context->put(tubepress_impl_feed_UrlBuilderChain::CHAIN_KEY_PROVIDER_NAME, $provider);

        return $context;
    }
}


