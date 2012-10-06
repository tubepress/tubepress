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

/**
 * Adds shortcode handlers to TubePress.
 */
class tubepress_plugins_core_impl_listeners_PlayerLocationsRegistrar
{
    public function onBoot(ehough_tickertape_api_Event $bootEvent)
    {
        $playerLocations = array(

            new tubepress_plugins_core_impl_player_JqModalPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_NormalPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_PopupPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_ShadowboxPluggablePlayerLocationService(),
            new tubepress_plugins_core_impl_player_StaticPluggablePlayerLocationService()
        );

        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        foreach ($playerLocations as $playerLocation) {

            $serviceCollectionsRegistry->registerService(

                tubepress_spi_player_PluggablePlayerLocationService::_,
                $playerLocation
            );
        }
    }
}
