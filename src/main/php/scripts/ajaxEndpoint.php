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

/*
 * WordPress stubbornly will not load except from the global scope.
 */
if (strpos(realpath(__FILE__), 'wp-content' . DIRECTORY_SEPARATOR . 'plugins') !== false ||true) {

	require '/var/www/tubepress_testing_ground/wp-blog-header.php';
    //require '/var/www/gshd/wordpress/wp-blog-header.php';
    //include dirname(__FILE__) . '/../../../../../../../wp-blog-header.php';
}

/**
 * Boot tubepress.
 */
require 'boot.php';

/**
 * Hand off the request to the Ajax handler.
 */
tubepress_impl_patterns_ioc_KernelServiceLocator::getAjaxHandler()->handle();
