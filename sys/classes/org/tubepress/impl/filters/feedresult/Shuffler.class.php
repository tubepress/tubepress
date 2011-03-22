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
 * Shuffles videos on request.
 */
class org_tubepress_impl_filters_feedresult_Shuffler
{
	public function filter()
	{
		$args       = func_get_args();
		$feedResult = $args[0];
		$videos     = $feedResult->getVideoArray();

		if (!is_array($videos)) {
			//log
			return $feedResult;
		}

		$ioc            = org_tubepress_impl_ioc_IocContainer::getInstance();
		$tpom           = $ioc->get('org_tubepress_api_options_OptionsManager');
		
	    /* shuffle if we need to */
        if ($tpom->get(org_tubepress_api_const_options_names_Display::ORDER_BY) == org_tubepress_api_const_options_values_OrderValue::RANDOM) {
            org_tubepress_impl_log_Log::log('Shuffler', 'Shuffling videos');
            shuffle($videos);
        }
		
		/* modify the feed result */
		$feedResult->setVideoArray($videos);
		
		return $feedResult;
	}
}

$ioc      = org_tubepress_impl_ioc_IocContainer::getInstance();
$fm       = $ioc->get('org_tubepress_api_patterns_FilterManager');
$instance = $ioc->get('org_tubepress_impl_filters_feedresult_Shuffler');

$fm->registerFilter(org_tubepress_api_const_filters_ExecutionPoint::VIDEOS_DELIVERY, array($instance, 'filter'));