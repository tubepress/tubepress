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

/**
 * Trims down the number of results based on various criteria.
 */
class org_tubepress_impl_filters_feedresult_ResultCountCapper
{
    const LOG_PREFIX = 'Result Count Capper';

    public function filter()
    {
        $args         = func_get_args();
        $feedResult   = $args[0];
        $totalResults = $feedResult->getEffectiveTotalResultCount();
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $tpom         = $ioc->get('org_tubepress_api_options_OptionsManager');
        $limit        = $tpom-> get(org_tubepress_api_const_options_names_Feed::RESULT_COUNT_CAP);
        $firstCut     = $limit == 0 ? $totalResults : min($limit, $totalResults);
        $secondCut    = min($firstCut, self::_calculateRealMax($tpom, $firstCut));
        $videos       = $feedResult->getVideoArray();
        $resultCount  = count($videos);

        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Effective total result count (taking into account user-defined limit) is %d video(s)', $secondCut);

        if ($resultCount > $secondCut) {
            org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Result has %d video(s), limit is %d. So we\'re chopping it down.', $resultCount, $secondCut);
            $feedResult->setVideoArray(array_splice($videos, 0, $secondCut - $resultCount));
        }

        $feedResult->setEffectiveTotalResultCount($secondCut);
        return $feedResult;
    }

    private static function _calculateRealMax($tpom, $reported)
    {
        $mode = $tpom->get(org_tubepress_api_const_options_names_Output::MODE);

        switch ($mode) {
            case org_tubepress_api_const_options_values_ModeValue::TAG:
                //http://code.google.com/apis/youtube/2.0/reference.html#Videos_feed
                return 999;
            case org_tubepress_api_const_options_values_ModeValue::FAVORITES:
                //http://code.google.com/apis/youtube/2.0/reference.html#User_favorites_feed
                return 50;
            case org_tubepress_api_const_options_values_ModeValue::PLAYLIST:
                //http://code.google.com/apis/youtube/2.0/reference.html#Playlist_feed
                return 200;
        }

        return $reported;
    }
}

$ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
$fm       = $ioc->get('org_tubepress_api_patterns_FilterManager');
$instance = $ioc->get('org_tubepress_impl_filters_feedresult_ResultCountCapper');

$fm->registerFilter(org_tubepress_api_const_filters_ExecutionPoint::VIDEOS_DELIVERY, array($instance, 'filter'));
