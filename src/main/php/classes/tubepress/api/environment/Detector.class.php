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
 * Detects TubePress's environment
 */
interface org_tubepress_api_environment_Detector
{
    const _ = 'org_tubepress_api_environment_Detector';

    /**
     * Detects if the user is running TubePress Pro.
     *
     * @return boolean True is the user is running TubePress Pro. False otherwise (or if there is a problem detecting the environment).
     */
    function isPro();

    /**
     * Detects if the user is running within WordPress
     *
     * @return boolean True is the user is running within WordPress (or if there is a problem detecting the environment). False otherwise.
     */
    function isWordPress();
}
