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
class tubepress_plugins_core_wordpresscore_WordPressCore
{
    public static function registerWordPressListeners()
    {
        $environmentDetector = tubepress_impl_patterns_ioc_KernelServiceLocator::getEnvironmentDetector();

        if (! $environmentDetector->isWordPress()) {

            //short circuit
            return false;
        }

        $eventDispatcher = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();

        $eventDispatcher->addListener(tubepress_api_event_Boot::EVENT_NAME,
            array(new tubepress_plugins_wordpresscore_listeners_WordPressIocContainerBuilder(), 'onBoot'));

        $eventDispatcher->addListener(tubepress_api_event_Boot::EVENT_NAME,
            array(new tubepress_plugins_wordpresscore_listeners_WordPressApiIntegrator(), 'onBoot'));

        return true;
    }
}

if (tubepress_plugins_core_wordpresscore_WordPressCore::registerWordPressListeners()) {

    if (! function_exists('wp_cron')) {

        /*
        * This is a little ugly, but it's the only way I know to
        * properly load WordPress if required. Please remember that this
        * code *cannot* be put inside of a class.
        */
        include '/var/www/gshd/wordpress/wp-blog-header.php';
//    include TUBEPRESS_ROOT . '/../../../wp-blog-header.php';
    }

}