<?php
/**
Plugin Name: TubePress
Plugin URI: http://tubepress.org
Description: Displays gorgeous YouTube and Vimeo galleries in your posts, pages, and/or sidebar. Upgrade to <a href="http://tubepress.org/download/">TubePress Pro</a> for more features!
Author: Eric D. Hough
Version: 2.2.5
Author URI: http://ehough.com

Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)

This file is part of TubePress (http://tubepress.org)

TubePress is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

TubePress is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with TubePress.  If not, see <http://www.gnu.org/licenses/>.
*/

if (version_compare(PHP_VERSION, '5.0.2', '>=')) {

    class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/sys/classes/org/tubepress/impl/classloader/ClassLoader.class.php';
    org_tubepress_impl_classloader_ClassLoader::loadClasses(array('org_tubepress_impl_ioc_IocContainer'));
    
    org_tubepress_impl_ioc_IocContainer::getInstance()->get('org_tubepress_api_bootstrap_Bootstrapper')->boot();
}
