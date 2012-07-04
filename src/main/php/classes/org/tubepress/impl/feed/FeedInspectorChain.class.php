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
    'org_tubepress_api_feed_FeedInspector',
    'org_tubepress_spi_patterns_cor_Chain',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_api_provider_ProviderResult',
    'org_tubepress_impl_ioc_IocContainer',
    'org_tubepress_impl_feed_FeedInspectorChainContext',
));

/**
 * Sends the feed to the right inspection service based on the provider.
 */
class org_tubepress_impl_feed_FeedInspectorChain implements org_tubepress_api_feed_FeedInspector
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

            org_tubepress_impl_log_Log::log('Feed inspector chain', 'Caught exception while counting: ' . $e->getMessage());
            throw $e;
        }
    }

    private function _wrappedCount($rawFeed)
    {
        $ioc          = org_tubepress_impl_ioc_IocContainer::getInstance();
        $pc           = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $chain        = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);
        $providerName = $pc->calculateCurrentVideoProvider();
        $context      = $chain->createContextInstance();

        $context->providerName = $providerName;
        $context->rawFeed      = $rawFeed;

        /* let the commands do the heavy lifting */
        $status = $chain->execute($context, array(
            'org_tubepress_impl_feed_inspection_YouTubeFeedInspectionCommand',
            'org_tubepress_impl_feed_inspection_VimeoFeedInspectionCommand'
        ));

        if ($status === false) {
            throw new Exception('No commands could inspect the feed');
        }

        return $context->returnValue;
    }
}
