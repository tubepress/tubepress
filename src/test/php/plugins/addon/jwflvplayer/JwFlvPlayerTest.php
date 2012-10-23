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
class tubepress_plugins_jwflvplayer_JwFlvPlayerTest extends TubePressUnitTest
{
    private $_mockServiceCollectionsRegistry;

	function setup()
	{
        $this->_mockServiceCollectionsRegistry = Mockery::mock(tubepress_spi_patterns_sl_ServiceCollectionsRegistry::_);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setServiceCollectionsRegistry($this->_mockServiceCollectionsRegistry);
	}

	function testJwFlvPlayer()
    {
        $this->_mockServiceCollectionsRegistry->shouldReceive('registerService')->once()->with(

            tubepress_spi_embedded_PluggableEmbeddedPlayerService::_,
            Mockery::type('tubepress_plugins_jwflvplayer_impl_embedded_JwFlvPluggableEmbeddedPlayerService'));

        require TUBEPRESS_ROOT . '/src/main/php/plugins/addon/jwflvplayer/JwFlvPlayer.php';

        $this->assertTrue(true);
    }
}