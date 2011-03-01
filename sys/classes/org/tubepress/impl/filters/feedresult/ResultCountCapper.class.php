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
        $limit        = $limit == 0 ? $totalResults : min($limit, $totalResults);
        
        org_tubepress_impl_log_Log::log(self::LOG_PREFIX, 'Effective total result count (taking into account user-defined limit) is %d video(s)', $limit);
        
        $feedResult->setEffectiveTotalResultCount($limit);
        return $feedResult;
    }
}

$ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
$fm       = $ioc->get('org_tubepress_api_patterns_FilterManager');
$instance = $ioc->get('org_tubepress_impl_filters_feedresult_ResultCountCapper');

$fm->registerFilter(org_tubepress_api_const_filters_ExecutionPoint::VIDEOS_DELIVERY, array($instance, 'filter'));