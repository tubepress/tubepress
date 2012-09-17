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

/**
 * Sends the feed to the right inspection service based on the provider.
 */
class tubepress_impl_feed_FeedInspectorChain implements tubepress_spi_feed_FeedInspector
{
    /**
     * Provider name chain key.
     */
    const CHAIN_KEY_PROVIDER_NAME = 'providerName';

    /**
     * Raw feed chain key.
     */
    const CHAIN_KEY_RAW_FEED = 'rawFeed';

    /**
     * Video count chain key.
     */
    const CHAIN_KEY_COUNT = 'videoCount';

    /** @var \ehough_chaingang_api_Chain */
    private $_chain;

    /** @var \ehough_epilog_api_ILogger */
    private $_logger;

    public function __construct(ehough_chaingang_api_Chain $chain)
    {
        $this->_chain  = $chain;
        $this->_logger = ehough_epilog_api_LoggerFactory::getLogger('Feed Inspector Chain');
    }

    /**
     * Count the total videos in this feed result.
     *
     * @param mixed $rawFeed The raw video feed (varies depending on provider)
     *
     * @return int The total result count of this query, or 0 if there was a problem.
     */
    public function getTotalResultCount($rawFeed)
    {
        try {

            return $this->_wrappedCount($rawFeed);

        } catch (Exception $e) {

            $this->_logger->warn(sprintf('Caught exception while counting: ' . $e->getMessage()));

            return 0;
        }
    }

    private function _wrappedCount($rawFeed)
    {
        $providerCalculatorService = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoProviderCalculator();

        $providerName = $providerCalculatorService->calculateCurrentVideoProvider();

        $context = new ehough_chaingang_impl_StandardContext();
        $context->put(self::CHAIN_KEY_PROVIDER_NAME, $providerName);
        $context->put(self::CHAIN_KEY_RAW_FEED, $rawFeed);

        /* let the commands do the heavy lifting */
        $status = $this->_chain->execute($context);

        if ($status === false) {

            $this->_logger->warn('No commands could handle execution.');

            return 0;
        }

        return $context->get(self::CHAIN_KEY_COUNT);
    }
}
