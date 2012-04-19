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

class_exists('org_tubepress_impl_classloader_ClassLoader') || require dirname(__FILE__) . '/../../../classloader/ClassLoader.class.php';
org_tubepress_impl_classloader_ClassLoader::loadClasses(array(
    'org_tubepress_api_const_options_names_Feed',
    'org_tubepress_api_const_options_names_Output',
    'org_tubepress_api_const_options_values_GallerySourceValue',
    'org_tubepress_api_exec_ExecutionContext',
    'org_tubepress_api_provider_ProviderResult',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Trims down the number of results based on various criteria.
 */
class org_tubepress_impl_plugin_filters_providerresult_ResultCountCapper
{
    const LOG_PREFIX = 'Result Count Capper';

    public function alter_providerResult(org_tubepress_api_provider_ProviderResult $providerResult)
    {
        $totalResults = $providerResult->getEffectiveTotalResultCount();
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $context      = $ioc->get(org_tubepress_api_exec_ExecutionContext::_);
        $limit        = $context->get(org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $firstCut     = $limit == 0 ? $totalResults : min($limit, $totalResults);
        $secondCut    = min($firstCut, self::_calculateRealMax($context, $firstCut));
        $videos       = $providerResult->getVideoArray();
        $resultCount  = count($videos);

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Effective total result count (taking into account user-defined limit) is %d video(s)', $secondCut);

        if ($resultCount > $secondCut) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Result has %d video(s), limit is %d. So we\'re chopping it down.', $resultCount, $secondCut);
            $providerResult->setVideoArray(array_splice($videos, 0, $secondCut - $resultCount));
        }

        $providerResult->setEffectiveTotalResultCount($secondCut);
        return $providerResult;
    }

    private static function _calculateRealMax($context, $reported)
    {
        $mode = $context->get(org_tubepress_api_const_options_names_Output::GALLERY_SOURCE);

        switch ($mode) {
            case org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_SEARCH:
                //http://code.google.com/apis/youtube/2.0/reference.html#Videos_feed
                return 999;
            case org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_FAVORITES:
                //http://code.google.com/apis/youtube/2.0/reference.html#User_favorites_feed
                return 50;
            case org_tubepress_api_const_options_values_GallerySourceValue::YOUTUBE_PLAYLIST:
                //http://code.google.com/apis/youtube/2.0/reference.html#Playlist_feed
                return 200;
        }

        return $reported;
    }
}
