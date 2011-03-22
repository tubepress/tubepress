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

function_exists('tubepress_load_classes')
    || require dirname(__FILE__) . '/../../../../tubepress_classloader.php';
tubepress_load_classes(array('org_tubepress_api_feed_FeedInspector',
    'org_tubepress_api_feed_FeedResult',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_api_patterns_StrategyManager'));

/**
 * Sends the feed to the right inspection service based on the provider.
 */
class org_tubepress_impl_feed_DelegatingFeedInspector implements org_tubepress_api_feed_FeedInspector
{
    /**
     * Count the total videos in this feed result.
     *
     * @param unknown $rawFeed The raw video feed (varies depending on provider)
     *
     * @return int The total result count of this query, or 0 if there was a problem.
     */
    public function getTotalResultCount($rawFeed)
    {
        try {
            return $this->_wrappedCount($rawFeed);
        } catch (Exception $e) {
            org_tubepress_impl_log_Log::log('Delegating Feed Inspector', 'Caught exception while counting: ' . $e->getMessage());
            return 0;
        }
    }
    
    private function _wrappedCount($rawFeed)
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $pc           = $ioc->get('org_tubepress_api_provider_ProviderCalculator');
        $sm           = $ioc->get('org_tubepress_api_patterns_StrategyManager');
        $providerName = $pc->calculateCurrentVideoProvider();

        /* let the strategies do the heavy lifting */
        return $sm->executeStrategy(array(
            'org_tubepress_impl_feed_inspectionstrategies_YouTubeFeedInspectionStrategy',
            'org_tubepress_impl_feed_inspectionstrategies_VimeoFeedInspectionStrategy'
        ), $providerName, $rawFeed);
    }
}
