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
 * Video factory that sends the feed to the right video factory based on the provider
 */
class tubepress_impl_factory_VideoFactoryChain implements tubepress_spi_factory_VideoFactory
{
    /**
     * Raw feed chain key.
     */
    const CHAIN_KEY_RAW_FEED = 'rawFeed';

    /**
     * Video array chain key.
     */
    const CHAIN_KEY_VIDEO_ARRAY = 'videoArray';

    private $_chain;

    public function __construct(ehough_chaingang_api_Chain $chain)
    {
        $this->_chain = $chain;
    }

    /**
     * Converts raw video feeds to TubePress videos
     *
     * @param mixed $feed The raw feed result from the video provider
     *
     * @return array an array of TubePress videos generated from the feed (may be empty).
     */
    public final function feedToVideoArray($feed)
    {
        $eventDispatcherService    = tubepress_impl_patterns_ioc_KernelServiceLocator::getEventDispatcher();
        $providerCalculatorService = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoProviderCalculator();
        $providerName              = $providerCalculatorService->calculateCurrentVideoProvider();
        $chainContext              = new ehough_chaingang_impl_StandardContext();

        $chainContext->put(self::CHAIN_KEY_RAW_FEED, $feed);

        /* let the commands do the heavy lifting */
        $status = $this->_chain->execute($chainContext);

        if ($status === false) {

            return array();
        }

        $videos = $chainContext->get(self::CHAIN_KEY_VIDEO_ARRAY);

        if (count($videos) === 0) {

            /** short circuit. */
            return $videos;
        }

        /**
         * Throw up an construction event for each video.
         */
        for ($x = 0; $x < count($videos); $x++) {

            $videoConstructionEvent = new tubepress_api_event_VideoConstruction(

                $videos[$x], array(

                    tubepress_api_event_VideoConstruction::ARGUMENT_PROVIDER_NAME => $providerName
                )
            );

            $eventDispatcherService->dispatch(

                tubepress_api_event_VideoConstruction::EVENT_NAME,
                $videoConstructionEvent
            );

            $videos[$x] = $videoConstructionEvent->getSubject();
        }

        return $videos;
    }
}
