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
    'org_tubepress_spi_patterns_cor_Chain',
    'org_tubepress_api_feed_UrlBuilder',
    'org_tubepress_api_provider_ProviderCalculator',
    'org_tubepress_spi_patterns_cor_Chain',
    'org_tubepress_impl_ioc_IocContainer',
));

/**
 * Builds URLs based on the current provider
 */
class org_tubepress_impl_feed_UrlBuilderChain implements org_tubepress_api_feed_UrlBuilder
{
    /**
     * Builds a URL for a list of videos
     *
     * @param int $currentPage The current page number of the gallery.
     *
     * @throws Exception If there was a problem.
     *
     * @return string The request URL for this gallery
     */
    public function buildGalleryUrl($currentPage)
    {
        return self::_build($currentPage, false);
    }

    /**
     * Builds a request url for a single video
     *
     * @param string $id The video ID to search for
     *
     * @throws Exception If there was a problem.
     *
     * @return string The URL for the single video given.
     */
    public function buildSingleVideoUrl($id)
    {
        return self::_build($id, true);
    }

    private static function _build($arg, $single)
    {
        $ioc   = org_tubepress_impl_ioc_IocContainer::getInstance();
        $pc    = $ioc->get(org_tubepress_api_provider_ProviderCalculator::_);
        $chain = $ioc->get(org_tubepress_spi_patterns_cor_Chain::_);

        if ($single) {
            $providerName = $pc->calculateProviderOfVideoId($arg);
        } else {
            $providerName = $pc->calculateCurrentVideoProvider();
        }

        $context               = $chain->createContextInstance();
        $context->providerName = $providerName;
        $context->single       = $single;
        $context->arg          = $arg;

        /* let the commands do the heavy lifting */
        $status = $chain->execute($context, array(
            'org_tubepress_impl_feed_urlbuilding_YouTubeUrlBuilderCommand',
            'org_tubepress_impl_feed_urlbuilding_VimeoUrlBuilderCommand'
        ));

        if ($status === false) {
            throw new Exception('No commands could build a URL');
        }

        return $context->returnValue;
    }
}
