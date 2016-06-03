<?php
/*
 * Copyright 2006 - 2016 TubePress LLC (http://tubepress.com)
 *
 * This file is part of TubePress (http://tubepress.com)
 *
 * This Source Code Form is subject to the terms of the Mozilla Public
 * License, v. 2.0. If a copy of the MPL was not distributed with this
 * file, You can obtain one at http://mozilla.org/MPL/2.0/.
 */

class tubepress_media_impl_listeners_CollectionListener
{
    /**
     * @var tubepress_spi_media_MediaProviderInterface[]
     */
    private $_mediaProviders;

    public function setMediaProviders(array $mediaProviders)
    {
        $this->_mediaProviders = $mediaProviders;
    }

    public function onMediaPageRequest(tubepress_api_event_EventInterface $event)
    {
        $source   = $event->getSubject();
        $provider = null;

        foreach ($this->_mediaProviders as $mediaProvider) {

            $sources = $mediaProvider->getGallerySourceNames();

            if (in_array($source, $sources)) {

                $provider = $mediaProvider;
                break;
            }
        }

        if ($provider === null) {

            return;
        }

        $page = $provider->collectPage($event->getArgument('pageNumber'));

        foreach ($page->getItems() as $mediaItem) {

            $mediaItem->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_PROVIDER, $provider);
        }

        $event->setArgument('mediaPage', $page);
    }

    public function onMediaItemRequest(tubepress_api_event_EventInterface $event)
    {
        $itemId   = $event->getSubject();
        $provider = null;

        foreach ($this->_mediaProviders as $mediaProvider) {

            if ($mediaProvider->ownsItem($itemId)) {

                $provider = $mediaProvider;
                break;
            }
        }

        if ($provider === null) {

            return;
        }

        $item = $provider->collectSingle($itemId);

        if ($item !== null) {

            $item->setAttribute(tubepress_api_media_MediaItem::ATTRIBUTE_PROVIDER, $provider);
        }

        $event->setArgument('mediaItem', $item);
    }
}
