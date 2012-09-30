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
 * Default video provider registry.
 */
class tubepress_impl_provider_DefaultVideoProviderRegistry implements tubepress_spi_provider_VideoProviderRegistry
{
    private $_providers = array();

    /**
     * Register a new video provider to TubePress.
     *
     * @param tubepress_spi_provider_VideoProvider $videoProvider The provider to register.
     *
     * @return mixed Null if the provider was successfully registered, otherwise a string error message.
     */
    public final function registerProvider(tubepress_spi_provider_VideoProvider $videoProvider)
    {
        if (isset($this->_providers[$videoProvider->getName()])) {

            return $videoProvider->getName() . ' has already been registered';
        }

        $this->_providers[$videoProvider->getName()] = $videoProvider;
    }

    /**
     * @return array An array of all registered video providers.
     */
    public final function getAllRegisteredProviders()
    {
        return array_values($this->_providers);
    }
}
