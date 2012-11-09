<?php
/**
 * Copyright 2006 - 2012 Eric D. Hough (http://ehough.com)
 *
 * This file is part of TubePress (http://tubepress.org)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

/**
 * A video provider that dispatches its results.
 */
abstract class tubepress_impl_provider_AbstractDispatchingPluggableVideoProviderService implements tubepress_spi_provider_PluggableVideoProviderService
{
    /**
     * Fetch a video gallery page.
     *
     * @param int $currentPage The requested page number of the gallery.
     *
     * @return tubepress_api_video_VideoGalleryPage The video gallery page for this page. May be empty, never null.
     */
    public final function fetchVideoGalleryPage($currentPage)
    {
        $result = $this->fetchVideoGalleryPageNoDispatch($currentPage);

        $eventDispatcher = tubepress_impl_patterns_sl_ServiceLocator::getEventDispatcher();

        $event = new tubepress_api_event_TubePressEvent($result);

        $eventDispatcher->dispatch(

            tubepress_api_const_event_CoreEventNames::VIDEO_GALLERY_PAGE_CONSTRUCTION,
            $event
        );

        return $event->getSubject();
    }

    /**
     * Fetch a video gallery page.
     *
     * @param int $currentPage The requested page number of the gallery.
     *
     * @return tubepress_api_video_VideoGalleryPage The video gallery page for this page. May be empty, never null.
     */
    protected abstract function fetchVideoGalleryPageNoDispatch($currentPage);
}
