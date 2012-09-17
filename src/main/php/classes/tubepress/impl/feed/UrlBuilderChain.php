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
 * Builds URLs based on the current provider
 */
class tubepress_impl_feed_UrlBuilderChain implements tubepress_spi_feed_UrlBuilder
{
    /**
     * Provider name key.
     */
    const CHAIN_KEY_PROVIDER_NAME = 'providerName';

    /**
     * Is single key.
     */
    const CHAIN_KEY_IS_SINGLE = 'isSingle';

    /**
     * Argument key.
     */
    const CHAIN_KEY_ARGUMENT = 'arg';

    /**
     * URL key.
     */
    const CHAIN_KEY_URL = 'url';

    /**
     * @var ehough_chaingang_api_Chain
     */
    private $_chain;

    public function __construct(ehough_chaingang_api_Chain $chain)
    {
        $this->_chain = $chain;
    }

    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @return string The request URL for this gallery.
     */
    public final function buildGalleryUrl($currentPage)
    {
        return $this->_build($currentPage, false);
    }

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws InvalidArgumentException If unable to build a URL for the given video.
     *
     * @return string The URL for the single video given.
     */
    public final function buildSingleVideoUrl($id)
    {
        return $this->_build($id, true);
    }

    private function _build($arg, $single)
    {
        $providerCalculatorService = tubepress_impl_patterns_ioc_KernelServiceLocator::getVideoProviderCalculator();

        if ($single) {

            $providerName = $providerCalculatorService->calculateProviderOfVideoId($arg);

        } else {

            $providerName = $providerCalculatorService->calculateCurrentVideoProvider();
        }

        $context = new ehough_chaingang_impl_StandardContext();
        $context->putAll(array(

            self::CHAIN_KEY_PROVIDER_NAME => $providerName,
            self::CHAIN_KEY_IS_SINGLE     => $single,
            self::CHAIN_KEY_ARGUMENT      => $arg
        ));

        /* let the commands do the heavy lifting */
        $status = $this->_chain->execute($context);

        if ($status === false) {

            throw new InvalidArgumentException('No commands could build a URL for ' . $arg);
        }

        return $context->get(self::CHAIN_KEY_URL);
    }
}
