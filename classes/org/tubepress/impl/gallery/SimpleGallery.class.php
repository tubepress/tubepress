<?php
/**
 * Copyright 2006 - 2010 Eric D. Hough (http://ehough.com)
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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_gallery_Gallery',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_log_Log',
    'org_tubepress_api_patterns_StrategyManager'));

/**
 * TubePress gallery. Generates HTML for TubePress.
 */
class org_tubepress_impl_gallery_SimpleGallery implements org_tubepress_api_gallery_Gallery
{
    const LOG_PREFIX = 'Gallery';

    /**
     * Generates the HTML for TubePress.
     *
     * @param string $shortCodeContent The optional shortcode content
     *
     * @return The HTML for TubePress.
     */
    public function getHtml($shortCodeContent = '')
    {
        $ioc = org_tubepress_impl_ioc_IocContainer::getInstance();

        /* do a bit of logging */
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Type of IOC container is %s', get_class($ioc));

        /* parse the shortcode if we need to */
        if ($shortCodeContent != '') {
            $shortcodeParser = $ioc->get('org_tubepress_api_shortcode_ShortcodeParser');
            $shortcodeParser->parse($shortCodeContent);
        }

        /* use the strategy manager to get the HTML */
        return $ioc->get('org_tubepress_api_patterns_StrategyManager')->executeStrategy(array(
            'org_tubepress_impl_gallery_strategies_SingleVideoStrategy',
            'org_tubepress_impl_gallery_strategies_SoloPlayerStrategy',
            'org_tubepress_impl_gallery_strategies_ThumbGalleryStrategy'
        ));
    }

}
