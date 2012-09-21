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
 * These classes are here for backwards compatability only.
 */
class org_tubepress_api_const_options_names_Meta extends tubepress_api_const_options_names_Meta {}

trigger_error('The "org_tubepress_ ..." prefixed classes are deprecated. Please update your code and replace all instances of '
    . '"org_tubepress_api_const_options_names_Meta" with "tubepress_api_const_options_names_Meta".', E_USER_NOTICE);