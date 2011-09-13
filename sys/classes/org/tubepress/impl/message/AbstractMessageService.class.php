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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php' ;
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_message_MessageService'
));

/**
 * Shared message functionality for org_tubepress_api_message_MessageService implementations.
 * This class basically provides one additional layer of abstraction between
 * the code and the actual message in the .pot files.
 */
abstract class org_tubepress_impl_message_AbstractMessageService implements org_tubepress_api_message_MessageService
{
    private $_msgs = array(
        'options-page-options-filter' => 'Only show options applicable to...',

        'options-category-title-output'   => 'Which videos?',
        'options-category-title-display'  => 'Appearance',
        'options-category-title-embedded' => 'Embedded Player',
        'options-category-title-meta'     => 'Meta Display',
        'options-category-title-feed'     => 'Provider Feed',
        'options-category-title-advanced' => 'Advanced',

        'no-videos-found'     => 'No matching videos',
        'search-input-button' => 'Search'
    );
}
