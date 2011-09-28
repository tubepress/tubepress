<?php
/**
 * Copyright 2006 - 2011 Eric D. Hough (http://ehough.com)
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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require(dirname(__FILE__) . '/../../classloader/ClassLoader.class.php');
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_spi_options_ui_TabsHtmlGenerator',
));

/**
 * Generates the "meat" of the options form (in the form of tabs).
 */
class org_tubepress_impl_options_ui_DefaultTabsHtmlGenerator implements org_tubepress_spi_options_ui_TabsHtmlGenerator
{
    /**
     * Generates the HTML for the "meat" of the options form.
     *
     * @return string The HTML for the options form.
     */
    public function getHtml()
    {
        return 'whatup';
    }
}
