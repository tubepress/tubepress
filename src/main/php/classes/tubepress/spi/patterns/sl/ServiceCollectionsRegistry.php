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
 * Maintains collections of pluggable services.
 */
interface tubepress_spi_patterns_sl_ServiceCollectionsRegistry
{
    const _ = 'tubepress_spi_patterns_sl_ServiceCollectionsRegistry';

    /**
     * Register a new service for use by TubePress.
     *
     * @param string $serviceType     The type of service to register.
     * @param object $serviceInstance The service instance.
     *
     * @return void
     */
    function registerService($serviceType, $serviceInstance);

    /**
     * @param string $serviceType The type of the service to find.
     *
     * @return array An array of all services of the given type. May be empty, never null.
     */
    function getAllServicesOfType($serviceType);
}
