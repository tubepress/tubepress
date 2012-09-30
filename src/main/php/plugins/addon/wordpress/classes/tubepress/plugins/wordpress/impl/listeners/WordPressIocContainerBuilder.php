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
 * Builds the WP IOC container and assigns it to the service locator.
 */
class tubepress_plugins_wordpress_impl_listeners_WordPressIocContainerBuilder
{
    public function onBoot(ehough_tickertape_api_Event $bootEvent)
    {
        $iocContainer   = new tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer();
        $messageService = $iocContainer->get(tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_MESSAGE);
        $storageManager = $iocContainer->get(tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_OPTIONS_STORAGE);
        $uiFormHandler  = $iocContainer->get(tubepress_plugins_wordpress_impl_patterns_ioc_WordPressIocContainer::SERVICE_OPTIONS_UI_FORMHANDLER);

        tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::setCoreIocContainer($iocContainer);

        tubepress_impl_patterns_ioc_KernelServiceLocator::setMessageService($messageService);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionStorageManager($storageManager);
        tubepress_impl_patterns_ioc_KernelServiceLocator::setOptionsUiFormHandler($uiFormHandler);
    }
}
