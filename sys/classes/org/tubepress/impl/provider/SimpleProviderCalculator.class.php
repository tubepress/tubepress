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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_provider_Provider',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Calculates video provider in use.
 */
class org_tubepress_impl_provider_SimpleProviderCalculator implements org_tubepress_api_provider_ProviderCalculator
{
    /**
     * Determine the current video provider.
     *
     * @return string 'youtube', 'vimeo', or 'directory'
     */
    public function calculateCurrentVideoProvider()
    {
        $ioc     = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $video   = $context->get(org_tubepress_api_const_options_names_Output::VIDEO);
        $pc      = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);

        /* requested a single video, and it's not vimeo or directory, so must be youtube */
        if ($video != '') {
            return $pc->calculateProviderOfVideoId($video);
        }

        /* calculate based on gallery content */
        $currentMode = $context->get(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE);
        if (strpos($currentMode, 'vimeo') === 0) {
            return org_tubepress_api_provider_Provider::VIMEO;
        }
        if (strpos($currentMode, 'directory') === 0) {
            return org_tubepress_api_provider_Provider::DIRECTORY;
        }
        return org_tubepress_api_provider_Provider::YOUTUBE;
    }

    public function calculateProviderOfVideoId($videoId)
    {
        if (is_numeric($videoId) === true) {
            return org_tubepress_api_provider_Provider::VIMEO;
        }
        if (preg_match_all('/^.*\.[A-Za-z]{3}$/', $videoId, $arr, PREG_PATTERN_ORDER) === 1) {
            return org_tubepress_api_provider_Provider::DIRECTORY;
        }
        return org_tubepress_api_provider_Provider::YOUTUBE;
    }
}
