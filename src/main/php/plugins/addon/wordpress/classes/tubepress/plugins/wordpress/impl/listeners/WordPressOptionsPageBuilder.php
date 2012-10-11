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
 * Builds the options page for TubePress, if necessary.
 */
class tubepress_plugins_wordpress_impl_listeners_WordPressOptionsPageBuilder
{
    public function onBoot(ehough_tickertape_api_Event $bootEvent)
    {
        $wordPressFunctionWrapper = tubepress_plugins_wordpress_impl_patterns_ioc_WordPressServiceLocator::getWordPressFunctionWrapper();

        if (! $wordPressFunctionWrapper->is_admin()) {

            //we only want to do this stuff on the admin page
            return;
        }

        $serviceCollectionsRegistry = tubepress_impl_patterns_ioc_KernelServiceLocator::getServiceCollectionsRegistry();

        $tabs = array(

            new tubepress_impl_options_ui_tabs_GallerySourceTab(),
            new tubepress_impl_options_ui_tabs_ThumbsTab(),
            new tubepress_impl_options_ui_tabs_EmbeddedTab(),
            new tubepress_impl_options_ui_tabs_MetaTab(),
            new tubepress_impl_options_ui_tabs_ThemeTab(),
            new tubepress_impl_options_ui_tabs_FeedTab(),
            new tubepress_impl_options_ui_tabs_CacheTab(),
            new tubepress_impl_options_ui_tabs_AdvancedTab()
        );

        foreach ($tabs as $tab) {

            $serviceCollectionsRegistry->registerService(

                tubepress_spi_options_ui_PluggableOptionsPageTab::CLASS_NAME,
                $tab
            );
        }
    }
}